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
        else {
            $jql[] = 'updatedDate >= "-60d"';
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

        $taskTypeMapping = array_map(
            function($mapRule) {
                return array_pop(array_values($mapRule));
            }, $mapping['Task']['TaskType']['mapping']
        );

        foreach( $result['issues'] as $issue )
        {
            if ( $issue['fields']['issuetype']['subtask'] || in_array($issue['fields']['issuetype']['name'], $taskTypeMapping) ) {
                $item = $second[$issue['key']] = array (
                    'class' => 'Task',
                    'id' => $issue['key']
                );
                $class = 'Task';
            }
            else {
                $item = $first[$issue['key']] = array (
                    'class' => 'Request',
                    'id' => $issue['key']
                );
            }
            $latest = array_merge( $latest,
                $this->getReferenceItems($issue, $item, $internalTimeStamp)
            );
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
        foreach( $this->jsonGet(self::apiPath.'/issuetype', array(), false) as $issueType ) {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
            if ( $issueType['subtask'] ) $this->taskIssueType = $issueType['id'];
        }

        foreach( $this->jsonGet(self::apiPath.'/status', array(), false) as $issueState ) {
            $this->issueStates[$issueState['id']] = $issueState['name'];
        }
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['updated'] != '' && strtotime($timestamp) > strtotime($item['updated']) ) return false;
        if ( $timestamp != '' && $item['created'] != '' && strtotime($timestamp) > strtotime($item['created']) ) return false;
        return true;
    }

    public function getSearchUrl( $ids )
    {
        $ids = array_filter($ids, function($value) {
            return $value != '';
        });
        return trim($this->getObjectIt()->get('URL'),' \\/') . '/issues/?jql=id%20in%20('.join('%2C',$ids).')';
    }

    protected function checkMappedValueExists( $fieldName, $value )
    {
        switch( $fieldName ) {
            case 'fields.issuetype.name':
                return in_array($value, $this->issueTypeMap);
            case 'fields.status.name':
                return in_array($value, $this->issueStates);
            default:
                return true;
        }
    }

    private $issueTypeMap = array();
    private $issueStates = array();
    private $taskIssueType = 0;
}