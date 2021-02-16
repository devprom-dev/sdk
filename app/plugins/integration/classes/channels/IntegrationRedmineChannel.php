<?php
use Netcarver\Textile\Parser;

class IntegrationRedmineChannel extends IntegrationRestAPIChannel
{
    const apiPath = "";

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();
        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone("UTC"));
            $time->modify("+1 second");
            $jql['updated_on'] = '>='.array_shift(preg_split('/\+/', $time->format(DateTime::ATOM))).'Z';
        }

        $releases = array();
        $issues = array();
        $hours = array();
        $nextTimestamp = '';
        $mapping = $this->getMapping();

        try {
            $leftItems = $limit;
            $offset = 0;
            while( $leftItems > 0 && $offset < $limit  ) {
                $jql['limit'] = $limit;
                $jql['offset'] = $offset;
                $result = $this->jsonGet('/projects/'.$this->getObjectIt()->get('ProjectKey').'/versions.json', $jql, false);
                foreach( $result['versions'] as $version ) {
                    if ( is_object($time) ) {
                        if ( DateTime::createFromFormat(DateTime::ATOM, $version['updated_on']) < $time ) continue;
                    }
                    $releases[] = array (
                        'class' => 'Release',
                        'id' => $version[$this->getKeyField()]
                    );
                }
                if ( $result['limit'] > 0 ) {
                    $offset += $result['limit'];
                    $leftItems = $result['total_count'] - $offset;
                }
                else {
                    $leftItems = 0;
                }
            }
        }
        catch( \Exception $e ) {
        }

        try {
            $issuesJql = $jql;
            $issuesJql['sort'] = 'updated_on:desc';
            $issuesJql['status_id'] = '*';
            $result = $this->jsonGet(
                '/projects/'.$this->getObjectIt()->get('ProjectKey').'/issues.json',
                array_merge(
                    $issuesJql,
                    array(
                        'limit' => 1
                    )
                )
            );

            $total = $result['total_count'];
            $leftItems = min($total, $limit);
            $offset = max(0, $total - $limit);

            while( $leftItems > 0 && $offset >= 0 )
            {
                $issuesJql['limit'] = $limit;
                $issuesJql['offset'] = $offset;
                $result = $this->jsonGet('/projects/'.$this->getObjectIt()->get('ProjectKey').'/issues.json', $issuesJql);

                foreach( $result['issues'] as $issue ) {
                    if ( $nextTimestamp == '' ) $nextTimestamp = $issue['updated_on'];

                    $redmineIssueType = array($issue['tracker']['name']);
                    $mappedIssueTypes = array_map(
                        function($item) {
                            return array_shift($item);
                        }, $mapping['Request']['Type']['mapping']);
                    $mappedTaskTypes = array_map(
                        function($item) {
                            return array_shift($item);
                        }, $mapping['Task']['TaskType']['mapping']);

                    $class = count(array_intersect($redmineIssueType, $mappedIssueTypes)) > 0
                        ? 'Request'
                        : (count(array_intersect($redmineIssueType, $mappedTaskTypes)) > 0
                            ? 'Task'
                            : 'Request');

                    $id = $issue[$this->getKeyField()];
                    $item = $issues[$id] = array (
                        'class' => $class,
                        'id' => $id
                    );

                    $issueDetails = $this->jsonGet('/issues/'.$id.'.json?include=journals,attachments', array());
                    $issues += $this->getReferenceItems($issueDetails, $item, $timestamp);
                }
                if ( $result['limit'] > 0 ) {
                    $leftItems -= min($result['total_count'], $result['limit']);
                    $offset -= min($result['total_count'], $result['limit']);
                }
                else {
                    $leftItems = 0;
                }
            }
        }
        catch( \Exception $e ) {
        }

        try {
            $leftItems = $limit;
            $offset = 0;
            while( $leftItems > 0 && $offset < $limit ) {
                $jql['limit'] = $limit;
                $jql['offset'] = $offset;
                $result = $this->jsonGet('/projects/' . $this->getObjectIt()->get('ProjectKey') . '/time_entries.json', $jql);
                foreach ($result['time_entries'] as $item) {
                    $hours[$item[$this->getKeyField()]] = array(
                        'class' => 'ActivityRequest',
                        'id' => $item[$this->getKeyField()]
                    );
                }
                if ( $result['limit'] > 0 ) {
                    $offset += $result['limit'];
                    $leftItems = $result['total_count'] - $offset;
                }
                else {
                    $leftItems = 0;
                }
            }
        }
        catch( \Exception $e ) {
        }

        ksort($issues);
        return array(
            array_merge(
                $releases, $issues, $hours
            ),
            $nextTimestamp != '' ? \DateTime::createFromFormat(\DateTime::ATOM, $nextTimestamp) : ''
        );
    }

    protected function getUserEmailAttribute() {
        return 'mail';
    }

    public function readItem($mapping, $class, $id, $parms = array())
    {
        switch( $class ) {
            case 'RequestComment':
                $result = $this->jsonGet($this->buildIdUrl(rtrim($mapping['url'],'/'),$parms['{parent}']));
                $comment = array_shift(
                    array_filter($result['issue']['journals'], function($item) use($id) {
                        return $item['id'] == $id && $item['notes'] != '';
                    })
                );
                if ( count($comment) < 1 ) return array();
                return $this->mapToInternal( $class, $id,
                    array_merge($comment, $parms),
                    $mapping,
                    function($data, $attribute) {
                        $attribute_path = preg_split('/\./',$attribute);
                        $value = $data[array_shift($attribute_path)];
                        foreach( $attribute_path as $field ) {
                            $value = $value[$field];
                        }
                        return $value;
                    }
                );
            default:
                return parent::readItem($mapping, $class, $id, $parms);
        }
    }

    public function mapToInternal($class, $id, $source, $mapping, $getter)
    {
        $data = parent::mapToInternal($class, $id, $source, $mapping, $getter);

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'File' ) {
                $data[$attribute] = $this->convertToFileAttribute($this->binaryGet($value));
            }
            if ( $attribute == 'Description' ) {
                if ( class_exists(\Netcarver\Textile\Parser::class) ) {
                    $parser = new \Netcarver\Textile\Parser();
                    $data[$attribute] = $parser->parse($data[$attribute]);
                }
            }
        }

        return $data;
    }

    public function mapFromInternal($class, $id, $source, $mapping, $setter)
    {
        $put = parent::mapFromInternal($class, $id, $source, $mapping, $setter);

        foreach( $put['issue'] as $item => $value ) {
            switch($item) {
                case 'tracker':
                    $typeMap = array_flip($this->issueTypeMap);
                    $put['issue']['tracker_id'] = $typeMap[$value['name']];
                    $put['issue']['tracker']['id'] = $typeMap[$value['name']];
                    break;
                case 'status':
                    $statusMap = array_flip($this->issueStates);
                    $put['issue']['status_id'] = $statusMap[$value['name']];
                    $put['issue']['status']['id'] = $statusMap[$value['name']];
                    break;
                case 'priority':
                    $map = array_flip($this->priorities);
                    $put['issue']['priority_id'] = $map[$value['name']];
                    $put['issue']['priority']['id'] = $map[$value['name']];
                    break;
                case 'assigned_to':
                    $put['issue']['assigned_to_id'] = $value['id'];
                    break;
            }
        }

        $projectMap = array_flip($this->projects);
        $put['issue']['project_id'] = $projectMap[$this->getObjectIt()->get('ProjectKey')];

        return $put;
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
        return array();
    }

    function buildDictionaries()
    {
        $data = $this->jsonGet(self::apiPath.'/trackers.json', array(), false);
        foreach( $data['trackers'] as $issueType ) {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
        }
        $data = $this->jsonGet(self::apiPath.'/issue_statuses.json', array(), false);
        foreach( $data['issue_statuses'] as $issueState ) {
            $this->issueStates[$issueState['id']] = $issueState['name'];
        }
        $data = $this->jsonGet(self::apiPath.'/enumerations/issue_priorities.json', array(), false);
        foreach( $data['issue_priorities'] as $value ) {
            $this->priorities[$value['id']] = $value['name'];
        }
        $data = $this->jsonGet(self::apiPath.'/projects/'.$this->getObjectIt()->get('ProjectKey').'.json', array(), false);
        $this->projects[$data['project']['id']] = $data['project']['identifier'];
    }

    protected function buildUsersMap()
    {
        $map = array();
        try {
            $users = $this->jsonGet(
                self::apiPath.'/users.json',
                array(),
                false
            );
            foreach( $users as $user ) {
                $map[$user['name']] = $user[$this->getUserEmailAttribute()];
            }
        }
        catch( \Exception $e ) {
            $map = array(0);
            $this->getLogger()->error($e->getMessage().$e->getTraceAsString());
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

    function getKeyValue($data)
    {
        $value = parent::getKeyValue($data);
        if ( $value != '' ) return $value;

        $firstKey = array_shift(array_keys($data));
        if ( is_numeric($firstKey) ) {
            $data = array_shift($data);
        }

        $key = array_shift(array_keys($data));
        return $data[$key][$this->getKeyField()];
    }

    private $issueTypeMap = array();
    private $issueStates = array();
    private $projects = array();
    private $priorities = array();
}