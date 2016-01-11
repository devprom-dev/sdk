<?php

class IntegrationJiraChannel extends IntegrationChannel
{
    const apiPath = "/rest/api/latest";

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();
        if ( $timestamp != '' ) {
            $jql[] = 'updatedDate >= "'.strftime("%Y/%m/%d %H:%M", strtotime($timestamp)) .'"';
        }
        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            $jql[] = 'project = '.$this->getObjectIt()->get('ProjectKey');
        }

        $result = $this->jsonGet('/rest/api/latest/search', array (
            'jql' => join(' AND ', $jql),
            'maxResults' => $limit,
            'fields' => '*all'
        ));

        $mapping = $this->getMapping();

        // extract items ids
        $first = array();
        $second = array();
        $latest = array();

        foreach( $result['issues'] as $issue )
        {
            if ( $issue['fields']['issuetype']['subtask'] || $issue['fields']['issuetype']['name'] == 'Task' ) {
                $second[$issue['key']] = array (
                    'class' => 'Task',
                    'id' => $issue['key']
                );
                $class = 'Task';
            }
            else {
                $first[$issue['key']] = array (
                    'class' => 'Request',
                    'id' => $issue['key']
                );
                $class = 'Request';
            }

            // append references into the items queue
            foreach( $mapping[$class] as $attribute => $column )
            {
                if ( !is_array($column) ) continue;
                if ( $column['type'] == '' || $column['reference'] == '' ) continue;

                $attribute_path = preg_split('/\./',$column['reference']);
                $value = $issue[array_shift($attribute_path)];
                foreach( $attribute_path as $field ) $value = $value[$field];

                if ( $value[$this->getKeyField()] == '' ) {
                    // one-to-many
                    foreach( $value as $item ) {
                        $latest[] = array (
                            'class' => $column['type'],
                            'id' => $item[$this->getKeyField()],
                            'parentId' => $issue['key']
                        );
                    }
                }
                else {
                    // one-to-one
                    if ( $value['key'] != '' ) $value['id'] = $value['key'];
                    $id = $value[$this->getKeyField()];
                    $first[$id] = array (
                        'class' => $column['type'],
                        'id' => $id
                    );
                }
            }
        }

        $releases = array();
        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            $result = $this->jsonGet('/rest/api/latest/project/'.$this->getObjectIt()->get('ProjectKey').'/versions');
            foreach( $result as $version ) {
                $releases[] = array (
                    'class' => 'Release',
                    'id' => $version[$this->getKeyField()]
                );
            }
        }

        // aggregate items using dependency based order
        return array_merge(
            $releases, $first, $second, $latest
        );
    }

    public function readItem($mapping, $class, $id, $parms = array())
    {
        $data = $this->mapToInternal(
            array_merge(
                $this->jsonGet($mapping['url'].'/'.$id, array('expand' => 'renderedBody,renderedFields')),
                $parms
            ),
            $mapping,
            function($data, $attribute) {
                $attribute_path = preg_split('/\./',$attribute);
                $value = $data[array_shift($attribute_path)];
                foreach( $attribute_path as $field ) {
                    list($field, $shift) = preg_split('/:/', $field);
                    if ( $shift == 'first' ) {
                        $value = array_shift($value[$field]);
                    }
                    else {
                        $value = $value[$field];
                    }
                }
                return $value;
            }
        );

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'File' ) {
                $data[$attribute] = $this->convertToFileAttribute($this->binaryGet($value));
            }
            if ( $attribute == 'Capacity' ) {
                $data[$attribute] = $data[$attribute] / (60 * 60);
            }
        }

        return $data;
    }

    public function writeItem($mapping, $class, $id, $data)
    {
        if ( $class == 'RequestLink' ) {
            $mapping['SourceRequest'] = array (
                'reference' => 'inwardIssue',
                'type' => 'Request'
            );
        }

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'Capacity' ) {
                $data[$attribute] = round($data[$attribute] * 60 * 60, 0);
            }
        }

        $emails_map = array_flip($this->usersMap);
        $put = $this->mapFromInternal( $data, $mapping,
            function($attribute, $value) use($emails_map)
            {
                $attribute_path = preg_split('/\./',$attribute);
                foreach( array_reverse($attribute_path) as $field ) {
                    list($field, $shift) = preg_split('/:/', $field);
                    if ( $shift == 'first' ) {
                        if ( count(array_filter($value, function($v) { return $v != ''; })) < 1 ) {
                            // skip empty value object
                            return array();
                        }
                        $value = array( $field => array($value) );
                    }
                    else if ( $shift == 'intval' ) {
                        $value = array( $field => intval($value) );
                    }
                    else {
                        $value = array( $field => $value );
                    }
                    if ( $field == 'emailAddress' ) {
                        $value['name'] = $emails_map[$value[$field]];
                        if ( $value['name'] == '' ) return array();
                    }
                }
                return $value;
            }
        );

        // substitute project key
        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            if ( in_array($class, array('Request','Task')) ) {
                $put['fields']['project']['key'] = $this->getObjectIt()->get('ProjectKey');
            }
            if ( in_array($class, array('Release')) ) {
                $put['project'] = $this->getObjectIt()->get('ProjectKey');
            }
        }

        // substitute issue type
        switch( $class ) {
            case 'Task':
                if ( $data['ChangeRequest'] > 0 ) {
                    $put['fields']['issuetype']['id'] = $this->taskIssueType;
                }
                else {
                    $map = array_flip($this->issueTypeMap);
                    $put['fields']['issuetype']['id'] = $map['Task'];
                }
                break;
        }

        if ( is_subclass_of($class, 'Attachment') )
        {
            if ( $id == '' ) {
                $result = $this->filePost(
                    $this->getObjectIt()->get('URL').$mapping['url-append'],
                    $data['FilePath'],
                    $data['FileExt'],
                    $data['FileMime']
                );
            }
        }
        elseif ( $class == 'RequestLink' )
        {
            if ( $id == '' ) {
                $result = array(
                    $this->jsonPost($mapping['url'], $put, array('expand' => 'renderedBody'))
                );
            }
        }
        else {
            if ( $id != '' ) {
                $result = array (
                    $this->jsonPut($mapping['url'].'/'.$id, $put, array('expand' => 'renderedBody'))
                );
            }
            else {
                $result = array(
                    $this->jsonPost($mapping['url'], $put, array('expand' => 'renderedBody'))
                );
            }
        }
        return $result;
    }

    public function deleteItem($mapping, $class, $id)
    {
        try {
            return $this->jsonDelete($mapping['url'].'/'.$id, array('deleteSubtasks' => 'true'));
        }
        catch( Exception $e ) {
            return array();
        }
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
        if ( !in_array($class, array('Request','Task')) ) return array();
        return $this->jsonPost($mapping['url'].'/'.$id.'/remotelink',
            array (
                'globalId' => $link,
                'object' => array (
                    'url' => $link,
                    'title' => $title
                )
            )
        );
    }

    function buildDictionaries()
    {
        foreach( $this->jsonGet(self::apiPath.'/issuetype') as $issueType )
        {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
            if ( $issueType['subtask'] ) $this->taskIssueType = $issueType['id'];
        }
        $users = $this->jsonGet(self::apiPath.'/user/assignable/multiProjectSearch',
            array('projectKeys' => $this->getObjectIt()->get('ProjectKey'))
        );
        foreach( $users as $user ) {
            $this->usersMap[$user['name']] = $user['emailAddress'];
        }
    }

    private $issueTypeMap = array();
    private $taskIssueType = 0;
    private $usersMap = array();
}