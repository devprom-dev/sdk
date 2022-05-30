<?php

class IntegrationJiraChannel extends IntegrationRestAPIChannel
{
    const apiPath = "/rest/api/2";

    public function getKeyField() {
        return 'id';
    }

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();
        if ( $timestamp != '' ) {
            $jql[] = 'updated > "-'. abs(round((strtotime($this->getTimestamp()) - strtotime($timestamp)) / 60, 0)) .'m"';
        }
        else {
            $jql[] = 'updated >= "-6000d"';
        }
        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            $jql[] = 'project = '.$this->getObjectIt()->get('ProjectKey');
        }

        $mapping = $this->getMapping();
        if ( $timestamp != '' ) {
            $internalTimeStamp = $timestamp;
        }
        $taskTypeMapping = array_map(
            function($mapRule) {
                return array_pop(array_values($mapRule));
            }, $mapping['Task']['TaskType']['mapping']
        );

        // extract items ids
        $first = array();
        $second = array();
        $latest = array();
        $nextTimestamp = '';
        $startAt = 0;

        do {
            $result = $this->jsonGet(self::apiPath . '/search', array(
                'jql' => join(' AND ', $jql) . " ORDER BY updated ASC",
                'maxResults' => $limit,
                'startAt' => $startAt,
                'fields' => 'updated,created,issuetype,comment,worklog,attachment,issuelinks'
            ));

            foreach ($result['issues'] as $issue) {
                if ($issue['fields']['issuetype']['subtask'] || in_array($issue['fields']['issuetype']['name'], $taskTypeMapping)) {
                    $item = $second[$issue[$this->getKeyField()]] = array(
                        'class' => 'Task',
                        'id' => $issue[$this->getKeyField()]
                    );
                } else {
                    $item = $first[$issue[$this->getKeyField()]] = array(
                        'class' => 'Request',
                        'id' => $issue[$this->getKeyField()]
                    );
                }
                $latest = array_merge($latest,
                    $this->getReferenceItems($issue, $item, $internalTimeStamp)
                );
                if ($issue['fields']['updated'] > $nextTimestamp) $nextTimestamp = $issue['fields']['updated'];
            }

            if ( count($result['issues']) < 1 ) break;
            $startAt += $result['maxResults'];

        } while(true);

        $releases = array();
        $users = array();

        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            $result = $this->jsonGet(self::apiPath."/project/{$this->getObjectIt()->get('ProjectKey')}/versions",
                            array(), false);
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

            $result = $this->jsonGet(self::apiPath."/user/assignable/search?project={$this->getObjectIt()->get('ProjectKey')}",
                            array(), false);
            foreach( $result as $user ) {
                $users[] = array (
                    'class' => 'User',
                    'id' => $user['key'] != '' ? $user['key'] : $user['accountId']
                );
            }
        }

        // aggregate items using dependency based order
        return array(
            array_merge(
                $users, $releases, $first, $second, $latest
            ),
            $nextTimestamp != '' ? new \DateTime($nextTimestamp, new DateTimeZone("UTC")) : ''
        );
    }

    protected function getUserEmailAttribute() {
        return 'emailAddress';
    }

    protected function getHeaders()
    {
        return array (
            "X-Atlassian-Token: no-check"
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

    public function mapToInternal($class, $id, $source, $mapping, $getter)
    {
        $data = parent::mapToInternal($class, $id, $source, $mapping, $getter);

        foreach( $data as $attribute => $value ) {
            if ( in_array($attribute, array('File', 'Photo')) ) {
                $data[$attribute] = $this->convertToFileAttribute($this->binaryGet($value));
            }
            if ( $attribute == 'Capacity' ) {
                $data[$attribute] = $data[$attribute] / (60 * 60);
            }
            if ( $attribute == 'State' ) {
                $this->persistState($class, $value);
            }
        }

        if ( in_array($class, array('Issue','Request','Task')) ) {
            $customAttribute = getFactory()->getObject('pm_CustomAttribute');
            foreach( $this->fields as $fieldKey => $fieldName ) {
                if ( is_array($source['fields'][$fieldKey]) && count($source['fields'][$fieldKey]) < 1 ) continue;
                if ( $source['fields'][$fieldKey] == '' ) continue;
                $customAttributeIt = $customAttribute->getByRef('ReferenceName', $fieldKey);
                if ( $customAttributeIt->getId() == '' ) {
                    $customAttribute->getRegistry()->Create(
                        array(
                            'ReferenceName' => $fieldKey,
                            'Caption' => $fieldName,
                            'AttributeType' => 4,
                            'EntityReferenceName' => strtolower($class),
                            'OrderNum' => 200
                        )
                    );
                }
                $data[$fieldKey] = is_array($source['fields'][$fieldKey])
                    ? join(',', $source['fields'][$fieldKey])
                    : $source['fields'][$fieldKey];
            }
        }

        $data['key'] = $source['key'];
        return $data;
    }

    protected function persistState( $class, $stateValue )
    {
        $object = getFactory()->getObject($class);
        if ( !$object instanceof \MetaobjectStatable ) return;

        $stateObject = getFactory()->getObject($object->getStateClassName());
        $stateIt = $stateObject->getByRef('ReferenceName', $stateValue);
        if ( $stateIt->getId() != '' ) return;

        $data = $this->jsonGet(self::apiPath.'/status/'.$stateValue, array(), false);
        $stateObject->getRegistry()->Create(
            array(
                'Caption' => $data['name'],
                'ReferenceName' => $data['id'],
                'IsTerminal' => $data['statusCategory']['key'] == 'new'
                                    ? 'N'
                                    : ($data['statusCategory']['key'] == 'done' ? 'Y' : 'I'),
                'OrderNum' => $data['id']
            )
        );
    }

    public function mapFromInternal($class, $id, $source, $mapping, $setter)
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

        $put = parent::mapFromInternal($class, $id, $source, $mapping, $setter);

        if ( $this->getObjectIt()->get('ProjectKey') != '' ) {
            if ( strpos($mapping['url'], '/issue') !== false ) {
                $put['fields']['project']['key'] = $this->getObjectIt()->get('ProjectKey');
            }
            if ( strpos($mapping['url'], '/version') !== false ) {
                $put['project'] = $this->getObjectIt()->get('ProjectKey');
            }
        }
        if ( $class == 'Task' && $put['fields']['issuetype']['name'] == '' ) {
            $put['fields']['issuetype']['id'] = $this->taskIssueTypeId;
        }
        unset($put['fields'][$this->getKeyField()]);

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
        $projectData = $this->jsonGet(
            self::apiPath."/project/{$this->getObjectIt()->get('ProjectKey')}", array(), false);

        $typeObject = getFactory()->getObject('RequestType');
        foreach( $this->jsonGet(self::apiPath."/issuetype/project?projectId={$projectData['id']}", array(), false) as $issueType ) {
            $this->issueTypeMap[$issueType['id']] = $issueType['name'];
            if ( $issueType['subtask'] ) {
                $this->taskIssueTypeId = $issueType['id'];
            }
            $typeIt = $typeObject->getByRef('ReferenceName', $issueType['id']);
            if ( $typeIt->getId() == '' ) {
                $typeObject->getRegistry()->Create(
                    array(
                        'ReferenceName' => $issueType['id'],
                        'Caption' => $issueType['name']
                    )
                );
            }
        }

        foreach( $this->jsonGet(self::apiPath."/field", array(), false) as $fieldData ) {
            if ( !$fieldData['custom'] ) continue;
            if ( strpos($fieldData['schema']['custom'], 'customfieldtypes') === false ) continue;
            $this->fields[$fieldData['key']] = $fieldData['name'];
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
            default:
                return true;
        }
    }

    public function buildIdUrl($url, $id)
    {
        if ( strpos($url, '{key}') !== false ) {
            return $this->parseUrl($url);
        }
        return parent::buildIdUrl($url, $id);
    }

    public function getWebLink( $id, $data, $link_pattern ) {
        return str_replace('{key}', $data['key'], parent::getWebLink( $id, $data, $link_pattern ));
    }

    private $issueTypeMap = array();
    private $taskIssueTypeId = 0;
    private $fields = array();
}