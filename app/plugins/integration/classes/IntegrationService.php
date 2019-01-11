<?php
include "channels/IntegrationChannel.php";
include "channels/IntegrationDummyChannel.php";
include "channels/IntegrationRestAPIChannel.php";
include "channels/IntegrationDevpromChannel.php";
include "channels/IntegrationJiraChannel.php";
include "channels/IntegrationReviewBoardChannel.php";
include "channels/IntegrationSlackChannel.php";
include "channels/IntegrationRedmineChannel.php";
include "channels/IntegrationYouTrackChannel.php";

class IntegrationService
{
    public function __construct( $object_it, $logger, $curlDelay = 0 )
    {
        $this->logger = $logger;
        $this->object_it = $object_it;
        $this->mapping = array_pop(
            json_decode($this->object_it->getHtmlDecoded('MappingSettings'), true)
        );

        $this->self_channel = new IntegrationDevpromChannel($this->object_it, $this->getLogger());
        $this->self_channel->setMapping($this->mapping);

        $this->remote_channel = $this->buildIntegrationChannel();
        $this->remote_channel->setMapping($this->mapping);
        $this->remote_channel->setCurlDelay($curlDelay);
    }

    public function setItemsToProcess( $items ) {
        $this->itemsToProcess = $items;
    }

    public function getRemoteChannel() {
        return $this->remote_channel;
    }

    public function process()
    {
        $linkObject = getFactory()->getObject('pm_IntegrationLink');
        $status_text = '';
        $queue = array();

        try {
            $queue = $this->getItemsQueue($this->itemsToProcess);

            if ( count($queue['items']) < 1 ) {
                $this->getLogger()->info('Integration queue is empty for '.$this->object_it->getDisplayName());

                $this->object_it->object->modify_parms( $this->object_it->getId(),
                    array (
                        'StatusText' => ''
                    )
                );
                return;
            }
            else {
                $this->getLogger()->debug('Integration queue: '.var_export($queue,true));
            }

            $this->links = $this->restoreLinks();
            $this->buildLinks();

            // prepare integration channel for processing
            try {
                $this->remote_channel->buildDictionaries();
            }
            catch( Exception $e ) {
                $this->getLogger()->error($status_text.PHP_EOL.$e->getTraceAsString());
            }

            $read = $queue['channel'] == 'self'
                ? $this->self_channel : $this->remote_channel;
            $write = $queue['channel'] == 'self'
                ? $this->remote_channel : $this->self_channel;

            $this->getLogger()->info('Process items for '.$this->object_it->getDisplayName());

            $mapping = $read->getMapping();
            foreach( array_slice($queue['items'], 0, $this->itemsToProcess, true) as $key => $item )
            {
                try {
                    $classMapping = $mapping[$item['class']];

                    if ( count($classMapping) < 1 ) {
                        $this->getLogger()->info('Skip because of mapping is undefined for '.$item['class']);
                        unset($queue['items'][$key]);
                        continue;
                    }

                    $classMapping['originalUrl'] = $classMapping['url'];
                    $classMapping['originalAppendUrl'] = $classMapping['url-append'];

                    $classMapping['url'] = preg_replace('/\{parent\}/', $item['parentId'], $classMapping['url']);
                    if ( $classMapping['url-append'] != '' ) {
                        $classMapping['url-append'] = preg_replace('/\{parent\}/', $item['parentId'], $classMapping['url-append']);
                    }

                    if ( $item['action'] == 'delete' ) {
                        $id = $this->itemMapExternalId[$item['class'].$item['id']];
                        $linkPredicate = new FilterAttributePredicate('ObjectId', $item['id']);
                        if ( $id == '' ) {
                            $id = $this->externalIdMapInternalId[$item['id']];
                            $linkPredicate = new FilterAttributePredicate('ObjectId', $id);
                        }
                        if ( $id != '' ) {
                            $write->deleteItem($classMapping, $item['class'], $id);

                            $link_it = $linkObject->getRegistry()->Query(
                                array (
                                    new FilterAttributePredicate('ObjectClass', $item['class']),
                                    $linkPredicate
                                )
                            );
                            while( !$link_it->end() ) {
                                $link_it->object->delete($link_it->getId());
                                $link_it->moveNext();
                            }
                        }
                        unset($queue['items'][$key]);
                        continue;
                    }

                    $data = $read->readItem( $classMapping, $item['class'], $item['id'],
                        array (
                            '{parent}' => $item['parentId'],
                            '{parentId}' => $this->externalIdMapInternalId[$item['parentId']]
                        )
                    );
                    $result = $data;
                    array_walk_recursive($result, function(&$value, $key) { $value = substr($value,0,32); });
                    $this->getLogger()->debug($item['class'].': '.var_export($result, true));

                    if ( count($data) < 2 ) {
                        $this->getLogger()->error('Unable read item: '.var_export($item, true));
                    }
                    else {
                        // build external link to the item
                        $link_pattern = $classMapping['link'];
                        if ( $link_pattern != '' && $data['SourceId'] == '' ) {
                            $data['SourceId'] = $this->object_it->get('URL').
                                preg_replace('/\{parentId\}/', $item['parentId'],
                                    preg_replace('/\{id\}/', $item['id'], $link_pattern));
                        }

                        // map external ID to internal one
                        if ( $data['SourceId'] != '' ) {
                            $data['Id'] = $this->urlMapInternalId[$data['SourceId']];
                        }

                        // map internal ID to external one
                        if ( $this->itemMapExternalId[$data['SourceId']] != '' ) {
                            // extract ID from link
                            $data['Id'] = $this->itemMapExternalId[$data['SourceId']];
                        }

                        // map internal parent ID to external one
                        foreach( $classMapping as $attribute => $column ) {
                            if ( $column == '{parentId}' ) {
                                $parentObject = getFactory()->getObject($item['class'])->getAttributeObject($attribute);
                                $data['{parent}'] = $this->itemMapExternalId[get_class($parentObject).$data[$attribute]['Id']];

                                $classMapping['url'] = preg_replace('/\{parent\}/', $data['{parent}'], $classMapping['originalUrl']);
                                if ( $classMapping['originalAppendUrl'] != '' ) {
                                    $classMapping['url-append'] = preg_replace('/\{parent\}/', $data['{parent}'], $classMapping['originalAppendUrl']);
                                }
                            }
                        }

                        $results = $write->writeItem( $classMapping, $item['class'], $data['Id'], $data, $item );

                        if ( $data['Id'] == '' ) {
                            foreach( $results as $result )
                            {
                                $id = $this->self_channel->getKeyValue($result);
                                if ( $id > 0 && $write instanceof IntegrationDevpromChannel )
                                {
                                    $linkObject->getRegistry()->Merge(
                                        array (
                                            'ObjectId' => $id,
                                            'ObjectClass' => $item['class'],
                                            'URL' => $data['SourceId'],
                                            'Integration' => $this->object_it->getId(),
                                            'ExternalId' => $item['id']
                                        ),
                                        array(
                                            'Integration', 'ExternalId', 'ObjectClass'
                                        )
                                    );
                                    $this->links[$item['class'] . $id] = $data['SourceId'];
                                    $internalId = $id;
                                    $externalId = $item['id'];
                                }

                                $remoteId = $this->remote_channel->getKeyValue($result);
                                if ( $remoteId != '' && $read instanceof IntegrationDevpromChannel )
                                {
                                    $id = $result['key'] != '' ? $result['key'] : $remoteId;
                                    $url = $this->object_it->get('URL') .
                                        preg_replace('/\{parent\}/', $data['{parent}'],
                                            preg_replace('/\{id\}/', $id, $classMapping['link']));

                                    $linkObject->getRegistry()->Merge(
                                        array(
                                            'ObjectId' => $item['id'],
                                            'ObjectClass' => $item['class'],
                                            'URL' => $url,
                                            'Integration' => $this->object_it->getId(),
                                            'ExternalId' => $id
                                        ),
                                        array(
                                            'Integration', 'ExternalId', 'ObjectClass'
                                        )
                                    );
                                    $this->links[$item['class'] . $item['id']] = $url;
                                    $internalId = $item['id'];
                                    $externalId = $id;
                                }

                                if ( $internalId != '' && $externalId != '' ) {
                                    $uid = new ObjectUID();
                                    $info = $uid->getUIDInfo(getFactory()->getObject($item['class'])->getExact($internalId));
                                    try {
                                        $this->remote_channel->storeLink($classMapping, $item['class'], $externalId, $info['url'], $info['uid']);
                                    }
                                    catch( Exception $e ) {
                                        $this->getLogger()->info($e->getMessage());
                                    }
                                }
                            }

                            array_walk_recursive($results, function(&$value, $key) { $value = substr($value,0,128); });
                            $this->getLogger()->debug($item['class'].': '.var_export($results,true));

                            $this->buildLinks();
                        }
                    }
                }
                catch( \Exception $e ) {
                    $this->getLogger()->info($e->getMessage());
                    if ( strpos($e->getMessage(), 'Skip') === false ) {
                        $status_text = $e->getMessage();
                    }
                }
                unset($queue['items'][$key]);
            }
        }
        catch( \Exception $e ) {
            $status_text = $e->getMessage();
            $this->getLogger()->error($status_text.PHP_EOL.$e->getTraceAsString());
        }

        $this->object_it->object->modify_parms( $this->object_it->getId(),
            array (
                'ItemsQueue' => json_encode($queue),
                'StatusText' => $status_text
            )
        );
        $this->getLogger()->debug('Integration queue: '.var_export($queue,true));
        $this->getLogger()->info('Integration process has been completed for '.$this->object_it->getDisplayName());
    }

    protected function getItemsQueue( $limit = 256 )
    {
        $queue = json_decode($this->object_it->getHtmlDecoded('ItemsQueue'), true);
        if ( count($queue['items']) > 0 ) return $queue;

        if ( in_array($this->object_it->get('Type'), array('write','readwrite')) )
        {
            list( $items, $timestamp ) = $this->self_channel->getItems($queue['self_timestamp'], $limit);
            if ( $timestamp instanceof \DateTime ) {
                $timestamp = $timestamp->format('Y-m-d H:i:s');
            }
            else {
                $timestamp = $this->self_channel->getTimestamp();
            }
            $queue['items'] = $items;
            $queue['channel'] = 'self';
            $queue['self_timestamp'] = $timestamp;
        }
        if ( count($queue['items']) < 1 && in_array($this->object_it->get('Type'), array('read','readwrite')) )
        {
            list( $items, $timestamp ) = $this->remote_channel->getItems($queue['remote_timestamp'], $limit);
            if ( $timestamp instanceof \DateTime ) {
                $timestamp = $timestamp->format('Y-m-d H:i:s');
            }
            else {
                $timestamp = $this->remote_channel->getTimestamp();
            }
            $queue['items'] = $items;
            $queue['channel'] = 'remote';
            $queue['remote_timestamp'] = $timestamp;
        }
        return $queue;
    }

    protected function buildIntegrationChannel()
    {
        switch( $this->object_it->get('Caption') ) {
            case 'jirarest':
                return new IntegrationJIRAChannel($this->object_it, $this->getLogger());
            case 'reviewboard':
                return new IntegrationReviewBoardChannel($this->object_it, $this->getLogger());
            case 'slack':
                return new IntegrationSlackChannel($this->object_it, $this->getLogger());
            case 'redmine':
                return new IntegrationRedmineChannel($this->object_it, $this->getLogger());
            case 'youtrack':
                return new IntegrationYouTrackChannel($this->object_it, $this->getLogger());
            default:
                return new IntegrationDummyChannel($this->object_it);
        }
    }

    protected function restoreLinks()
    {
        $links = array();
        $registry = getFactory()->getObject('pm_IntegrationLink')->getRegistry();
        $registry->setLimit('');
        $link_it = $registry->Query(
            array (
                new \FilterBaseVpdPredicate()
            )
        );
        while( !$link_it->end() ) {
            $links[$link_it->get('ObjectClass').$link_it->get('ObjectId')] = $link_it->getHtmlDecoded('URL');
            $link_it->moveNext();
        }
        return $links;
    }

    protected function buildLinks()
    {
        foreach( $this->links as $key => $link )
        {
            if ( !preg_match('/([A-Za-z]+)/', $key, $matches) ) continue;
            $this->itemMapExternalId[$key] = $this->extractId($this->mapping[$matches[1]]['link'], $link);
            $this->urlMapInternalId[$link] = str_replace($matches[1], '', $key);
            $this->externalIdMapInternalId[$this->itemMapExternalId[$key]] = $this->urlMapInternalId[$link];
        }
        $this->itemMapExternalId['Project'.getSession()->getProjectIt()->getId()] = $this->object_it->get('ProjectKey');
        $this->externalIdMapInternalId[$this->object_it->get('ProjectKey')] = getSession()->getProjectIt()->getId();

        $this->self_channel->setIdsMap($this->itemMapExternalId);
        $this->remote_channel->setIdsMap(array_flip($this->itemMapExternalId));
    }

    public function extractId( $link_pattern, $link )
    {
        if ( $link_pattern == '' ) return $link;

        $link_pattern = preg_replace( '/\\\{id\\\}/', '([^\/\&]+)', preg_quote($link_pattern, '/'));
        $link_pattern = preg_replace( '/\\\{parentId\\\}/', '[^\/\&\?]+', $link_pattern);

        if ( preg_match('/'.$link_pattern.'/i', $link, $matches) ) {
            $link = $matches[1];
        }
        return $link;
    }

    protected function getLogger() {
        return $this->logger;
    }

    private $object_it = null;
    private $remote_channel = null;
    private $self_channel = null;
    private $links = array();
    private $urlMapInternalId = array();
    private $itemMapExternalId = array();
    private $externalIdMapInternalId = array();
    private $mapping = array();
    private $itemsToProcess = 60;
    private $logger = null;
}
