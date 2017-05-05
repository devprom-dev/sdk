<?php

class IntegrationRedmineChannel extends IntegrationRestAPIChannel
{
    const apiPath = "";

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();
        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone("UTC"));
            $jql['updated_on'] = urlencode('>=').$time->format(DateTime::ISO8601);
        }

        $releases = array();
        $issues = array();
        $hours = array();

        try {
            $result = $this->jsonGet('/projects/'.$this->getObjectIt()->get('ProjectKey').'/versions.json', $jql, false);
            foreach( $result['versions'] as $version ) {
                $releases[] = array (
                    'class' => 'Release',
                    'id' => $version[$this->getKeyField()]
                );
            }
        }
        catch( \Exception $e ) {
        }

        try {
            $result = $this->jsonGet('/projects/'.$this->getObjectIt()->get('ProjectKey').'/issues.json', $jql);
            foreach( $result['issues'] as $issue )
            {
                $class = 'Request';
                $id = $issue[$this->getKeyField()];
                $item = $issues[$issue[$this->getKeyField()]] = array (
                    'class' => $class,
                    'id' => $id
                );

                $issueDetails = $this->jsonGet('/issues/'.$id.'.json?include=journals,attachments', array());
                $issues = array_merge( $issues,
                    $this->getReferenceItems($issueDetails, $item, $timestamp)
                );
            }
        }
        catch( \Exception $e ) {
        }

        try {
            $result = $this->jsonGet('/projects/'.$this->getObjectIt()->get('ProjectKey').'/time_entries.json', $jql);
            foreach( $result['time_entries'] as $item )
            {
                $hours[$item[$this->getKeyField()]] = array (
                    'class' => 'ActivityRequest',
                    'id' => $item[$this->getKeyField()]
                );
            }
        }
        catch( \Exception $e ) {
        }

        return array_merge(
            $releases, $issues, $hours
        );
    }

    protected function buildIdUrl( $url, $id ) {
        return str_replace(
            '{project}', $this->getObjectIt()->get('ProjectKey'),
                str_replace('{id}', $id, $url)
        );
    }

    protected function getUserEmailAttribute() {
        return 'mail';
    }

    protected function getHeaders()
    {
        return array (
            "Content-Type: application/json"
        );
    }

    public function mapToInternal($source, $mapping, $getter)
    {
        $data = parent::mapToInternal($source, $mapping, $getter);

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'File' ) {
                $data[$attribute] = $this->convertToFileAttribute($this->binaryGet($value));
            }
        }

        return $data;
    }

    public function mapFromInternal($source, $mapping, $setter)
    {
        $put = parent::mapFromInternal($source, $mapping, $setter);
        return $put;
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
        return array();
    }

    function buildDictionaries()
    {
        foreach( $this->jsonGet(self::apiPath.'/trackers.json', array(), false) as $issueType ) {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
        }
        foreach( $this->jsonGet(self::apiPath.'/issue_statuses.json', array(), false) as $issueState ) {
            $this->issueStates[$issueState['id']] = $issueState['name'];
        }
    }

    protected function buildUsersMap()
    {
        $map = array();
        $users = $this->jsonGet(
            self::apiPath.'/users.json',
            array(),
            false
        );
        foreach( $users as $user ) {
            $map[$user['name']] = $user[$this->getUserEmailAttribute()];
        }
        return $map;
    }

    protected function checkMappedValueExists( $fieldName, $value )
    {
        switch( $fieldName ) {
            case 'tracker.name':
                return count($this->issueTypeMap) < 1 || in_array($value, $this->issueTypeMap);
            case 'status.name':
                return count($this->issueStates) < 2 || in_array($value, $this->issueStates);
            default:
                return true;
        }
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['created_on'] != '' && strtotime($timestamp) > strtotime($item['created_on']) ) return false;
        return true;
    }

    private $issueTypeMap = array();
    private $issueStates = array();
    private $taskIssueType = 0;
}