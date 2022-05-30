<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class IntegrationDevpromRESTChannel extends IntegrationRestAPIChannel
{
    function getKeyField() {
        return 'Id';
    }

    function getWysiwygMode() {
        return ModelService::OUTPUT_ASIS;
    }

    public function getItems( $timestamp, $limit )
    {
        // build search query
        $jql = array();

        if ( $timestamp != '' ) {
            $time = new DateTime($timestamp, new DateTimeZone("UTC"));
            $time->modify("+1 second");
            $jql['updatedAfter'] = $time->format(DateTime::ISO8601);
        }


        $mapping = $this->getMapping();
        $nextTimestamp = '';
        $items = array();

        foreach( $mapping as $className => $mappingItem )
        {
            $page = 1;
            do {
                $result = $this->jsonGet($this->parseUrl($mappingItem['url']),
                                array_merge( $jql,
                                    array(
                                        'limit' => $limit,
                                        'page' => $page++
                                    )
                                )
                            );
                foreach( $result as $row ) {
                    $items[] = array(
                        'class' => $className,
                        'id' => $row['Id']
                    );
                    if ( $row['RecordModified'] > $nextTimestamp ) $nextTimestamp = $row['RecordModified'];
                }
            } while (count($result) >= $limit);
        }

        return array( $items,
            $nextTimestamp != ''
                ? new \DateTime($nextTimestamp, new DateTimeZone("UTC"))
                : ''
        );
    }

    protected function jsonGet( $url, $data = array(), $verbose = true ) {
        return parent::jsonGet($url, array_merge($data, array(
            'output' => ModelService::OUTPUT_ASIS
        )), $verbose);
    }

    public function parseUrl($url) {
        return str_replace('{project}', $this->projectId, $url);
    }

    public function getWebLink( $id, $data, $link_pattern ) {
        return $data['link'];
    }

    protected function getUserEmailAttribute() {
        return 'Email';
    }

    public function storeLink( $mapping, $class, $id, $link, $title ) {
        return array();
    }

    function buildDictionaries() {
        $this->projectId = $this->getObjectIt()->get('ProjectKey');
    }

    protected function buildUsersMap() {
        return array();
    }

    protected function checkNewItem( $timestamp, $item ) {
        if ( $timestamp != '' && $item['RecordCreated'] != '' && strtotime($timestamp) > strtotime($item['RecordCreated']) ) return false;
        return true;
    }

    private $projectId = '';
}