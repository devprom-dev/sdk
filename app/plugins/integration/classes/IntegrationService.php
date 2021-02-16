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
include "channels/IntegrationGitlabChannel.php";
include "channels/IntegrationTfsChannel.php";

class IntegrationService
{
    public function __construct( $object_it, $logger, $curlDelay = 0 )
    {
        $this->logger = $logger;
        $this->object_it = $object_it;
        $this->mapping = array_pop(
            json_decode($this->object_it->getHtmlDecoded('MappingSettings'), true)
        );
        $this->normalizeMapping($this->mapping);

        $this->self_channel = new IntegrationDevpromChannel($this->object_it, $this->getLogger());
        $this->self_channel->setMapping($this->mapping);

        $this->remote_channel = $this->buildIntegrationChannel();
        $this->remote_channel->setMapping($this->mapping);
        $this->remote_channel->setCurlDelay($curlDelay);

        $this->self_channel->setHtmlAllowed($this->remote_channel->getHtmlAllowed());
    }

    public function setItemsToProcess( $items ) {
        $this->itemsToProcess = $items;
    }

    public function getRemoteChannel() {
        return $this->remote_channel;
    }

    protected function normalizeMapping( &$mapping )
    {
        foreach( $mapping as $className => $classMapping ) {
            if ( $classMapping['link'] == '' ) {
                $classMapping['link'] = $classMapping['url'];
            }
        }
    }

    public function process()
    {
        $linkObject = getFactory()->getObject('pm_IntegrationLink');
        $status_text = '';
        $queue = array();

        try {
            // prepare integration channel for processing
            try {
                $this->remote_channel->buildDictionaries();
            }
            catch( Exception $e ) {
                $this->getLogger()->error($status_text.PHP_EOL.$e->getTraceAsString());
            }

            $queue = $this->getItemsQueue($this->itemsToProcess);

            if ( count($queue['items']) < 1 ) {
                $this->getLogger()->info('Integration queue is empty for '.$this->object_it->getDisplayName());

                $this->object_it->object->modify_parms( $this->object_it->getId(),
                    array (
                        'ItemsQueue' => json_encode($queue),
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

                    foreach( array('url','link','url-append') as $tag ) {
                        $classMapping[$tag] = $read->parseUrl($classMapping[$tag]);
                    }
                    $classMapping['originalAppendUrl'] = $classMapping['url-append'];

                    $additionalParms = array(
                        '{parent}' => $item['parentId']
                    );
                    foreach( $classMapping as $attribute => $column ) {
                        if ( $column == '{parentId}' ) {
                            $parentObject = getFactory()->getObject($item['class'])->getAttributeObject($attribute);
                            $additionalParms['{parentId}'] = $write instanceof IntegrationDevpromChannel
                                ? $this->linksExternal[get_class($parentObject).$item['parentId']]
                                : $this->links[get_class($parentObject).$item['parentId']];
                        }
                    }

                    foreach( array('url','link','url-append') as $tag ) {
                        $classMapping[$tag] = preg_replace('/\{parent\}/',
                            $write instanceof IntegrationDevpromChannel
                                ? $additionalParms['{parent}'] : $additionalParms['{parentId}'],
                                    $classMapping[$tag]);
                    }

                    if ( $item['action'] == 'delete' ) {
                        $id = $this->links[$item['class'].$item['id']];
                        $linkPredicate = new FilterAttributePredicate('ObjectId', $item['id']);
                        if ( $id == '' ) {
                            $id = $this->linksExternal[$item['class'].$item['id']];
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

                    $data = $read->readItem( $classMapping, $item['class'], $item['id'], $additionalParms);

                    $result = $data;
                    array_walk_recursive($result, function(&$value, $key) { $value = substr($value,0,32); });
                    $this->getLogger()->debug($item['class'].': '.var_export($result, true));

                    if ( count($data) < 2 ) {
                        $this->getLogger()->error('Unable read item: '.var_export($item, true));
                    }
                    else {
                        // map external ID to internal one
                        $data['Id'] = $write instanceof IntegrationDevpromChannel
                            ? $this->linksExternal[$item['class'] . $item['id']]
                            : $this->links[$item['class'] . $item['id']];

                        $classMapping['url-append'] = $classMapping['originalAppendUrl'];
                        foreach( array('url','url-append','link') as $tag ) {
                            if ( $classMapping[$tag] != '' ) {
                                $classMapping[$tag] = $write->parseUrl(
                                    preg_replace('/\{parent\}/', $additionalParms['{parentId}'], $classMapping[$tag])
                                );
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
                                            'URL' => $this->remote_channel->getWebLink( $item['id'], $data, $classMapping['link']),
                                            'Integration' => $this->object_it->getId(),
                                            'ExternalId' => $item['id']
                                        ),
                                        array(
                                            'Integration', 'ExternalId', 'ObjectClass'
                                        )
                                    );
                                    $this->links[$item['class'] . $id] = $item['id'];
                                    $internalId = $id;
                                    $externalId = $item['id'];
                                }

                                $remoteId = $this->remote_channel->getKeyValue($result);
                                if ( $remoteId != '' && $read instanceof IntegrationDevpromChannel )
                                {
                                    $id = $result['key'] != '' ? $result['key'] : $remoteId;
                                    $linkObject->getRegistry()->Merge(
                                        array(
                                            'ObjectId' => $item['id'],
                                            'ObjectClass' => $item['class'],
                                            'URL' => $this->remote_channel->getWebLink( $id, $result, $classMapping['link']),
                                            'Integration' => $this->object_it->getId(),
                                            'ExternalId' => $id
                                        ),
                                        array(
                                            'Integration', 'ExternalId', 'ObjectClass'
                                        )
                                    );
                                    $this->links[$item['class'] . $item['id']] = $id;
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
            case 'gitlab':
                return new IntegrationGitlabChannel($this->object_it, $this->getLogger());
            case 'tfs':
                return new IntegrationTfsChannel($this->object_it, $this->getLogger());
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
            $links[$link_it->get('ObjectClass').$link_it->get('ObjectId')] = $link_it->get('ExternalId');
            $link_it->moveNext();
        }
        return $links;
    }

    protected function buildLinks()
    {
        foreach( $this->links as $key => $link ) {
            if ( !preg_match('/([A-Za-z]+)/', $key, $matches) ) continue;
            $this->linksExternal[$matches[1] . $link] = str_replace($matches[1], '', $key);
        }
        $this->links['Project'.getSession()->getProjectIt()->getId()] = $this->object_it->get('ProjectKey');
        $this->linksExternal['Project'.$this->object_it->get('ProjectKey')] = getSession()->getProjectIt()->getId();

        $this->self_channel->setIdsMap($this->links, $this->linksExternal);
        $this->remote_channel->setIdsMap($this->links, $this->linksExternal);
    }

    protected function getLogger() {
        return $this->logger;
    }

    private $object_it = null;
    private $remote_channel = null;
    private $self_channel = null;
    private $links = array();
    private $linksExternal = array();
    private $mapping = array();
    private $itemsToProcess = 60;
    private $logger = null;
}
