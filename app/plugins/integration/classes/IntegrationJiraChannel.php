<?php

class IntegrationJiraChannel extends IntegrationRestAPIChannel
{
    const apiPath = "/rest/api/latest";

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();
        if ( $timestamp != '' ) {
            $jql[] = 'updatedDate >= "-'. round((strtotime(SystemDateTime::date()) - strtotime($timestamp)) / 60, 0) .'m"';
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
        if ( $timestamp != '' ) {
            $internalTimeStamp = $timestamp;
        }

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
                $class = 'Request';
                foreach( $mapping as $className => $classMapping ) {
                    if ( strpos($issue['self'], $classMapping['url']) !== false ) {
                        $class = $className;
                    }
                }
                $first[$issue['key']] = array (
                    'class' => $class,
                    'id' => $issue['key']
                );
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
                        if ( !$this->checkNewItem($internalTimeStamp, $item) ) continue; // skip non-modified items
                        $latest[] = array (
                            'class' => $column['type'],
                            'id' => $item[$this->getKeyField()],
                            'parentId' => $issue['key']
                        );
                    }
                }
                else {
                    // one-to-one
                    if ( !$this->checkNewItem($internalTimeStamp, $value) ) continue; // skip non-modified items
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
            $result = $this->jsonGet('/rest/api/latest/project/'.$this->getObjectIt()->get('ProjectKey').'/versions', array(), false);
            foreach( $result as $version ) {
                if ( $timestamp != '' ) {
                    // skip past releases
                    if((time()-(60*60*24)) > strtotime($version['releaseDate'])) continue;
                }
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

    protected function buildIdUrl( $url, $id ) {
        return $url . '/' . $id;
    }

    protected function getUserEmailAttribute() {
        return 'emailAddress';
    }

    protected function getHeaders()
    {
        return array (
            "X-Atlassian-Token: no-check",
            "Content-Type: application/json"
        );
    }

    protected function buildUsersMap()
    {
        $map = array();
        $users = $this->jsonGet(
            self::apiPath.'/user/assignable/multiProjectSearch',
            array('projectKeys' => $this->getObjectIt()->get('ProjectKey')),
            false
        );
        foreach( $users as $user ) {
            $map[$user['name']] = $user[$this->getUserEmailAttribute()];
        }
        return $map;
    }

    public function mapToInternal($source, $mapping, $getter)
    {
        $data = parent::mapToInternal($source, $mapping, $getter);

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

    public function mapFromInternal($source, $mapping, $setter)
    {
        if ( array_key_exists('SourceRequest', $mapping) ) {
            $mapping['SourceRequest'] = array (
                'reference' => 'inwardIssue',
                'type' => 'Request'
            );
        }

        foreach( $source as $attribute => $value ) {
            if ( $attribute == 'Capacity' ) {
                $source[$attribute] = round($source[$attribute] * 60 * 60, 0);
            }
        }

        $put = parent::mapFromInternal($source, $mapping, $setter);

        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            if ( strpos($mapping['url'], '/issue') !== false ) {
                $put['fields']['project']['key'] = $this->getObjectIt()->get('ProjectKey');
            }
            if ( strpos($mapping['url'], '/version') !== false ) {
                $put['project'] = $this->getObjectIt()->get('ProjectKey');
            }
        }

        return $put;
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
        foreach( $this->jsonGet(self::apiPath.'/issuetype', array(), false) as $issueType )
        {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
            if ( $issueType['subtask'] ) $this->taskIssueType = $issueType['id'];
        }
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['updated'] != '' && strtotime($timestamp) > strtotime($item['updated']) ) return false;
        if ( $timestamp != '' && $item['created'] != '' && strtotime($timestamp) > strtotime($item['created']) ) return false;
        return true;
    }

    private $issueTypeMap = array();
    private $taskIssueType = 0;
}