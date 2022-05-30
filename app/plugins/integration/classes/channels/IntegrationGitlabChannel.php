<?php
use Netcarver\Textile\Parser;

class IntegrationGitlabChannel extends IntegrationRestAPIChannel
{
    const apiPath = "/api/v4/";

    function getKeyField() {
        return 'iid';
    }

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();

        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone("UTC"));
            $time->modify("+1 second");
            $timestamp = array_shift(preg_split('/\+/', $time->format(DateTime::RFC3339_EXTENDED))).'Z';
            $jql['after'] = $time->format('Y-m-d');
        }

        $releases = array();
        $first = array();
        $latest = array();
        $nextTimestamp = '';

        $targetTypeMapping = array(
            'Issue' => 'Request',
            'Milestone' => getSession()->getProjectIt()->getMethodologyIt()->HasPlanning() ? 'Iteration' : 'Release',
            'Note' => 'RequestComment',
            'DiscussionNote' => 'RequestComment',
            'MergeRequest' => 'ReviewRequest'
        );

        if ( $timestamp == '' )
        {
            $items = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/milestones', array() );
            foreach( $items as $key => $item ) {
                $releases[$targetTypeMapping['Milestone'] . $item['id']] = array(
                    'class' => $targetTypeMapping['Milestone'],
                    'id' => $item['id']
                );
                if ( $item['updated_at'] > $nextTimestamp ) $nextTimestamp = $item['updated_at'];
            }

            $page = 1;
            do {
                $items = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/issues?per_page=30&page='.($page++), array() );
                foreach( $items as $key => $item ) {
                    $first['Request' . $item['iid']] = array(
                        'class' => 'Request',
                        'id' => $item['iid']
                    );
                    if ( $item['user_notes_count'] > 0 ) {
                        $notes = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/issues/'.$item['iid'].'/notes', array() );
                        foreach( $notes as $key => $note ) {
                            if ( $note['system'] ) continue;
                            $latest['RequestComment' . $note['id']] = array(
                                'class' => 'RequestComment',
                                'id' => $note['id'],
                                'parentId' => $item['iid']
                            );
                        }
                    }
                    if ( $item['updated_at'] > $nextTimestamp ) $nextTimestamp = $item['updated_at'];
                }
            } while (count($items) > 0);

            $page = 1;
            do {
                $items = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/merge_requests?per_page=30&page='.($page++), array() );
                foreach( $items as $key => $item ) {
                    if ( $item['created_at'] <= $timestamp ) continue;
                    $latest['ReviewRequest' . $item['id']] = array(
                        'class' => 'ReviewRequest',
                        'id' => $item['id']
                    );
                    if ( $item['created_at'] > $nextTimestamp ) $nextTimestamp = $item['created_at'];
                }
            } while (count($items) > 0);
        }
        else {
            $events = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/events', array (
                'jql' => join(' AND ', $jql)
            ));

            foreach( $events as $event ) {
                if ( $event['created_at'] <= $timestamp ) continue;

                $item = array(
                    'class' => $targetTypeMapping[$event['target_type']],
                    'id' => $event['target_iid']
                );
                $key = $item['class'] . $item['id'];

                switch( $item['class'] ) {
                    case 'Iteration':
                    case 'Release':
                        $item['id'] = $event['target_id'];
                        $key = $item['class'] . $item['id'];
                        $releases[$key] = $item;
                        break;
                    case 'Request':
                        $first[$key] = $item;
                        break;
                    case '':
                        break;
                    case 'RequestComment':
                        $item['parentId'] = $event['note']['noteable_iid'];
                    default:
                        $latest[$key] = $item;
                }
                if ( $event['created_at'] > $nextTimestamp ) $nextTimestamp = $event['created_at'];
            }
        }

        $page = 1;
        do {
            $items = $this->jsonGet(self::apiPath.'projects/'.$this->projectId.'/releases?per_page=30&page='.($page++), array() );
            foreach( $items as $key => $item ) {
                if ( $item['created_at'] <= $timestamp ) continue;
                $latest['Build' . $item['id']] = array(
                    'class' => 'Build',
                    'id' => $item['id']
                );
                if ( $item['created_at'] > $nextTimestamp ) $nextTimestamp = $item['created_at'];
            }
        } while (count($items) > 0);

        // aggregate items using dependency based order
        return array(
            array_merge(
                $releases, $first, $latest
            ),
            $nextTimestamp != '' ? new \DateTime($nextTimestamp, new DateTimeZone("UTC")) : ''
        );
    }

    public function parseUrl($url) {
        return str_replace('{project}', $this->projectId, $url);
    }

    public function getWebLink( $id, $data, $link_pattern ) {
        return $data['web_url'];
    }

    protected function getUserEmailAttribute() {
        return 'email';
    }

    public function storeLink( $mapping, $class, $id, $link, $title ) {
        return array();
    }

    public function mapToInternal($class, $id, $source, $mapping, $getter)
    {
        $data = parent::mapToInternal($class, $id, $source, $mapping, $getter);

        $data['web_url'] = $source['web_url'];
        if ( $data['iid'] == '' ) $data['iid'] = $data['id'];

        $states = array_intersect($source['labels'], $this->internalIssueStates);
        if ( count($states) > 0 ) {
            $data['State'] = array_shift($states);
        }

        $priorities = array_intersect($source['labels'], $this->internalPriorities);
        if ( count($priorities) > 0 ) {
            $data['Priority']['Caption'] = array_shift($priorities);
        }

        foreach( $data as $attribute => $value ) {
            if ( $attribute == 'Estimation' ) {
                $data[$attribute] = $value / (60 * 60);
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

        switch( $class ) {
            case 'Request':
                switch( $put['state'] ) {
                    case 'closed':
                        $put['state_event'] = 'close';
                        break;
                    case 'opened':
                        $put['state_event'] = 'reopen';
                        break;
                    default:
                        if ( !is_array($put['labels']) ) $put['labels'] = array();
                        $put['labels'] = array_merge(
                            array_diff($put['labels'], $this->internalIssueStates),
                            array(
                                $source['State']
                            )
                        );
                        $put['labels'] = array_merge(
                            array_diff($put['labels'], $this->internalPriorities),
                            array(
                                $source['Priority']['Caption']
                            )
                        );
                }

                if ($put['milestone']['iid'] != '' ) {
                    $put['milestone_id'] = $put['milestone']['iid'];
                }

                if ($put['assignee']['name'] != '' ) {
                    $name = $put['assignee']['name'];
                    $users = array_filter($this->users, function($user) use ($name) {
                        return $user['name'] == $name;
                    });

                    if ( count($users) > 0 ) {
                        $user = array_shift($users);
                        $put['assignee_ids'] = array(
                            $user['id']
                        );
                    }
                }


            break;
        }

        if ( $source['UID'] != '' && strpos($put['description'], $source['UID']) === false ) {
            $put['description'] = '['.$this->instanceName.']('.$source['URL'].') ' . PHP_EOL.PHP_EOL . $put['description'];
        }

        return $put;
    }

    function writeItem($mapping, $class, $id, $data, $queueItem)
    {
        $results = parent::writeItem($mapping, $class, $id, $data, $queueItem);
        foreach( $results as $key => $result ) {
            if ( $result['iid'] == '' || in_array($class, array('Release', 'Iteration')) ) {
                $results[$key]['iid'] = $result['id'];
            }
        }
        return $results;
    }

    function buildDictionaries()
    {
        $this->projectId = $this->getObjectIt()->get('ProjectKey');
        $this->instanceName = getFactory()->getObject('cms_SystemSettings')->getAll()->getDisplayName();
        $this->internalIssueStates = \WorkflowScheme::Instance()->getStates(getFactory()->getObject('Request'));
        $this->internalPriorities = getFactory()->getObject('Priority')->getAll()->fieldToArray('Caption');
    }

    protected function buildUsersMap()
    {
        $map = array();
        try {
            $this->users = $this->jsonGet(
                self::apiPath.'users',
                array(),
                false
            );
            foreach( $this->users as $user ) {
                $map[$user['username']] = $user[$this->getUserEmailAttribute()];
            }
        }
        catch( \Exception $e ) {
            $map = array(0);
            $this->getLogger()->error($e->getMessage().$e->getTraceAsString());
        }
        return $map;
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['created_at'] != '' && strtotime($timestamp) > strtotime($item['created_at']) ) return false;
        return true;
    }

    private $users = array();
    private $projectId = '';
    private $instanceName = '';
    private $internalIssueStates = array();
    private $internalPriorities = array();
}