<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class IntegrationTfsChannel extends IntegrationRestAPIChannel
{
    private $apiPath = '';
    private $apiVersion = '4.1';

    public function getKeyField() {
        return 'id';
    }

    public function getItems( $timestamp, $limit )
    {
        // build search query
        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone('UTC'));
            $time->modify("+1 second");
            $timestamp = $time->format(DateTime::ATOM);
        }

        $wiql = "Select [System.Id], [System.WorkItemType] From WorkItems Where [System.TeamProject] = '".$this->getObjectIt()->get('ProjectKey')."' ";
        if ( defined('TFS_QUERY_PREDICATE') ) $wiql .= TFS_QUERY_PREDICATE;

        $result = $this->jsonPost($this->apiPath . '/wit/wiql',
            array (
                'query' => $timestamp != ''
                    ? $wiql . " AND [System.ChangedDate] >= '".$timestamp."' Order By System.ChangedDate"
                    : $wiql . " Order By System.ChangedDate"
            ),
            array (
                'timePrecision' => 'true'
            )
        );

        $ids = array_map(function($item) {
                    return $item['id'];
                }, $result['workItems']);

        $internalTimeStamp = '';
        if ( $timestamp != '' ) {
            $internalTimeStamp = $timestamp;
        }

        // extract items ids
        $first = array();
        $second = array();
        $latest = array();
        $nextTimestamp = '';

        foreach( array_chunk($ids, 30) as $idsChunk ) {
            $result = $this->jsonGet($this->apiPath . '/wit/workitems', array(
                            'ids' => join(',', $idsChunk)
                        ), false);
            foreach( $result['value'] as $issue )
            {
                if ( $issue['fields']['System.WorkItemType'] == $this->taskIssueTypeName ) {
                    if ( defined('INTEGRATION_TFS_1') && $issue['fields']['Microsoft.VSTS.Scheduling.OriginalEstimate'] <= 0 ) continue;
                    $item = $second[$issue[$this->getKeyField()]] = array (
                        'class' => 'Task',
                        'id' => $issue[$this->getKeyField()]
                    );
                }
                else {
                    $item = $first[$issue[$this->getKeyField()]] = array (
                        'class' => 'Request',
                        'id' => $issue[$this->getKeyField()]
                    );
                }
                $latest = array_merge( $latest,
                    $this->getReferenceItems($issue, $item, $internalTimeStamp)
                );

                if ( $issue['fields']['System.ChangedDate'] > $nextTimestamp ) {
                    $nextTimestamp = $issue['fields']['System.ChangedDate'];
                }
            }
        }

        $result = $this->jsonGet($this->apiPath . '/wit/recyclebin', array(), false);
        foreach( $result['value'] as $issue )
        {
            /* needs to get it incrementally last since sync date/time
            $latest[] = array (
                'class' => 'Task',
                'id' => $issue[$this->getKeyField()],
                'action' => 'delete'
            );
            $latest[] = array (
                'class' => 'Request',
                'id' => $issue[$this->getKeyField()],
                'action' => 'delete'
            );
            */
        }

        // aggregate items using dependency based order
        return array(
            array_merge(
                $first, $second, $latest
            ),
            $nextTimestamp != '' ? new \DateTime($nextTimestamp, new DateTimeZone('UTC')) : ''
        );
    }

    protected function getUserEmailAttribute() {
        return 'uniqueName';
    }

    function getWysiwygMode() {
        return ModelService::OUTPUT_HTML;
    }

    protected function buildUsersMap()
    {
        $map = array();
        return $map;
    }

    public function mapToInternal($class, $id, $source, $mapping, $getter)
    {
        $data = parent::mapToInternal($class, $id, $source, $mapping, $getter);
        return $data;
    }

    public function mapFromInternal($class, $id, $source, $mapping, $setter)
    {
        $put = parent::mapFromInternal($class, $id, $source, $mapping, $setter);
        return $put;
    }

    function buildDictionaries()
    {
        $this->projectId = $this->getObjectIt()->get('ProjectKey');
        $this->apiPath = '/' . $this->projectId . '/_apis';

        $result = $this->jsonGet($this->apiPath . '/wit/workitemtypes', array(), false);
        foreach( $result['value'] as $issueType ) {
            if ( in_array($issueType['referenceName'], array('Microsoft.VSTS.WorkItemTypes.Task','Task','Задача')) ) {
                $this->taskIssueTypeName = $issueType['name'];
                break;
            }
        }
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
    }

    protected function checkNewItem( $timestamp, $item )
    {
        if ( $timestamp != '' && $item['System.ChangedDate'] != '' && strtotime($timestamp) > strtotime($item['System.ChangedDate']) ) return false;
        if ( $timestamp != '' && $item['System.CreatedDate'] != '' && strtotime($timestamp) > strtotime($item['System.CreatedDate']) ) return false;
        return true;
    }

    protected function buildAuthParms( $curl )
    {
        if ( $this->getObjectIt()->get('HttpUserPassword') != '' ) {
            curl_setopt($curl, CURLOPT_USERPWD,
                $this->getObjectIt()->getHtmlDecoded('HttpUserName') . ":" . $this->getObjectIt()->getHtmlDecoded('HttpUserPassword'));
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
        else {
            // use personal access token (no password required)
            curl_setopt($curl, CURLOPT_USERPWD, $this->getObjectIt()->get('HttpUserName'));
            curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }
    }

    protected function jsonGet( $url, $data = array(), $verbose = true )
    {
        $jsonData = parent::jsonGet( $url,
            array_merge($data, array('api-version' => $this->apiVersion, '$expand' => 'All')),
                $verbose );

        if ( is_array($jsonData['relations']) ) {
            // expand each relation into it's REST representation
            foreach( $jsonData['relations'] as $relKey => $relData ) {
                if ( $relData['rel'] == 'AttachedFile' ) continue;
                $relData['url'] = str_replace($this->getObjectIt()->get('URL'), '', $relData['url']);
                $relDetails = parent::jsonGet( $relData['url'],
                    array_merge($data, array('api-version' => $this->apiVersion, '$expand' => 'All')),
                        false );
                $jsonData['relations'][$relKey] = array_merge(
                    $jsonData['relations'][$relKey], $relDetails
                );
            }
        }

        return $jsonData;
    }

    protected function buildPostFields( $post ) {
        if ( array_key_exists('query', $post) ) return parent::buildPostFields($post);
        return $this->buildPatchFields($post);
    }

    protected function getPostHeaders( $url ) {
        if ( strpos($url, 'wiql') !== false ) return parent::getPostHeaders($url);
        return $this->getPatchHeaders();
    }

    protected function jsonPost( $url, $post = array(), $parms = array(), $verbose = true ) {
        if ( $post['fields']['System.WorkItemType'] != '' ) {
            $url = str_replace('{id}', '$'.rawurlencode($post['fields']['System.WorkItemType']), $url);
        }
        return parent::jsonPost($url, $post,
            array_merge($parms, array('api-version' => $this->apiVersion)), $verbose);
    }

    protected function jsonPut( $url, $post = array(), $parms = array(), $verbose = true ) {
        return parent::jsonPatch($url, $post,
            array_merge($parms, array('api-version' => $this->apiVersion)), $verbose);
    }

    private $taskIssueTypeName = '';
    private $projectId = '';
}