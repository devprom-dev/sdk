<?php
include "IntegrationChannel.php";
include "IntegrationDummyChannel.php";
include "IntegrationDevpromChannel.php";
include "IntegrationJiraChannel.php";

class IntegrationService
{
    public function __construct( $object_it )
    {
        $this->object_it = $object_it;
        $this->mapping = array_pop(
            json_decode($this->object_it->getHtmlDecoded('MappingSettings'), true)
        );

        $this->links = $this->restoreLinks();
        $this->buildLinks();

        $this->self_channel = new IntegrationDevpromChannel($this->object_it, $this->getLogger());
        $this->self_channel->setMapping($this->mapping);

        $this->remote_channel = $this->buildIntegrationChannel();
        $this->remote_channel->setMapping($this->mapping);

        $this->setIdsMapping();
    }

    public function process()
    {
        $linkObject = getFactory()->getObject('pm_IntegrationLink');
        $status_text = '';
        $queue = array();

        try {
            $queue = $this->getItemsQueue();

            if ( count($queue['items']) < 1 ) {
                $this->getLogger()->info('Integration queue is empty for '.$this->object_it->getDisplayName());
                return;
            }
            else {
                $this->getLogger()->debug('Integration queue: '.var_export($queue,true));
            }

            // prepare integration channel for processing
            $this->remote_channel->buildDictionaries();

            $read = $queue['channel'] == 'self'
                ? $this->self_channel : $this->remote_channel;
            $write = $queue['channel'] == 'self'
                ? $this->remote_channel : $this->self_channel;

            $this->getLogger()->info('Process items for '.$this->object_it->getDisplayName());

            $mapping = $read->getMapping();
            foreach( $queue['items'] as $key => $item )
            {
                try {
                    $classMapping = $mapping[$item['class']];

                    if ( $classMapping['url'] == '' )
                    {
                        $this->getLogger()->info('Skip because of mapping is undefined for '.$item['class']);
                        unset($queue['items'][$key]);
                        continue;
                    }

                    $classMapping['url'] = preg_replace('/\{parent\}/', $item['parentId'], $classMapping['url']);
                    if ( $classMapping['url-append'] != '' ) {
                        $classMapping['url-append'] = preg_replace('/\{parent\}/', $item['parentId'], $classMapping['url-append']);
                    }

                    if ( $item['action'] == 'delete' ) {
                        $id = $this->shortLinks[$item['class'].$item['id']];
                        $linkPredicate = new FilterAttributePredicate('ObjectId', $item['id']);
                        if ( $id == '' ) {
                            $id = $this->shortBackLinks[$item['id']];
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
                            '{parentId}' => $this->shortBackLinks[$item['parentId']]
                        )
                    );
                    $this->getLogger()->debug($item['class'].': '.var_export($data,true));

                    if ( count($data) < 1 ) {
                        $this->getLogger()->error('Unable read item '.var_export($item, true));
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
                            $data['Id'] = $this->backlinks[$data['SourceId']];
                        }

                        // map internal ID to external one
                        if ( $this->links[$data['SourceId']] != '' ) {
                            // extract ID from link
                            $data['Id'] = $this->shortLinks[$data['SourceId']];
                        }

                        // map internal parent ID to external one
                        foreach( $classMapping as $attribute => $column ) {
                            if ( $column == '{parentId}' ) {
                                $parentObject = getFactory()->getObject($item['class'])->getAttributeObject($attribute);
                                $data['{parent}'] = $this->shortLinks[get_class($parentObject).$data[$attribute]['Id']];
                            }
                        }

                        $results = $write->writeItem( $classMapping, $item['class'], $data['Id'], $data );
                        $this->getLogger()->debug($item['class'].': '.var_export($results,true));

                        foreach( $results as $result ) {
                            if ( $data['Id'] != '' ) continue;
                            $id = $result[$this->self_channel->getKeyField()];
                            if ( $id != '' ) {
                                $linkObject->add_parms(
                                    array (
                                        'ObjectId' => $id,
                                        'ObjectClass' => $item['class'],
                                        'URL' => $data['SourceId']
                                    )
                                );
                                $this->links[$item['class'].$id] = $data['SourceId'];
                                $internalId = $id;
                                $externalId = $item['id'];
                            }

                            if ( $result[$this->remote_channel->getKeyField()] != '' ) {
                                $id = $result['key'] != '' ? $result['key'] : $result[$this->remote_channel->getKeyField()];
                                $url = $this->object_it->get('URL').
                                            preg_replace('/\{parent\}/', $data['{parent}'],
                                                preg_replace('/\{id\}/', $id, $classMapping['link']));
                                $linkObject->add_parms(
                                    array (
                                        'ObjectId' => $item['id'],
                                        'ObjectClass' => $item['class'],
                                        'URL' => $url
                                    )
                                );
                                $this->links[$item['class'].$item['id']] = $url;
                                $internalId = $item['id'];
                                $externalId = $id;
                            }

                            if ( $internalId != '' && $externalId != '' ) {
                                $uid = new ObjectUID();
                                $info = $uid->getUIDInfo(getFactory()->getObject($item['class'])->getExact($internalId));
                                $write->storeLink($classMapping, $item['class'], $externalId, $info['url'], $info['uid']);
                            }
                        }

                        $this->buildLinks();
                        $this->setIdsMapping();
                    }
                }
                catch( Exception $e ) {
                    $status_text = $e->getMessage();
                }
                unset($queue['items'][$key]);
            }
        }
        catch( Exception $e ) {
            $status_text = $e->getMessage();
            $this->getLogger()->error($status_text.PHP_EOL.$e->getTraceAsString());
        }

        $this->object_it->object->modify_parms( $this->object_it->getId(),
            array (
                'ItemsQueue' => json_encode($queue),
                'StatusText' => $status_text
            )
        );
        $this->getLogger()->info('Integration process has been completed for '.$this->object_it->getDisplayName());
    }

    protected function getItemsQueue( $limit = 100 )
    {
        $queue = json_decode($this->object_it->getHtmlDecoded('ItemsQueue'), true);

        if ( count($queue['items']) < 1 )
        {
            if ( in_array($this->object_it->get('Type'), array('write','readwrite')) ) {
                $queue['items'] = $this->self_channel->getItems($queue['self_timestamp'], $limit);
                $queue['channel'] = 'self';
                $queue['self_timestamp'] = SystemDateTime::date();
            }
            if ( count($queue['items']) < 1 ) {
                $queue['items'] = $this->remote_channel->getItems($queue['remote_timestamp'], $limit);
                $queue['channel'] = 'remote';
                $queue['remote_timestamp'] = SystemDateTime::date();
            }
        }
        return $queue;
    }

    protected function buildIntegrationChannel()
    {
        switch( $this->object_it->get('Caption') ) {
            case 'jirarest':
                return new IntegrationJIRAChannel($this->object_it, $this->getLogger());
            default:
                return new IntegrationDummyChannel($this->object_it);
        }
    }

    protected function restoreLinks()
    {
        $links = array();
        $link_it = getFactory()->getObject('pm_IntegrationLink')->getAll();
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
            $this->shortLinks[$key] = $this->extractId($this->mapping[$matches[1]]['link'], $link);
            $this->backlinks[$link] = str_replace($matches[1], '', $key);
            $this->shortBackLinks[$this->shortLinks[$key]] = $this->backlinks[$link];
        }
        $this->shortLinks['Project'.getSession()->getProjectIt()->getId()] = $this->object_it->get('ProjectKey');
        $this->shortBackLinks[$this->object_it->get('ProjectKey')] = getSession()->getProjectIt()->getId();
    }

    protected function extractId( $link_pattern, $link )
    {
        if ( $link_pattern == '' ) return $link;

        $link_pattern = preg_replace( '/\\\{id\\\}/', '([^\/\&]+)', preg_quote($link_pattern, '/'));
        $link_pattern = preg_replace( '/\\\{parentId\\\}/', '[^\/\&\?]+', $link_pattern);

        if ( preg_match('/'.$link_pattern.'/i', $link, $matches) ) {
            $link = $matches[1];
        }
        return $link;
    }

    protected function setIdsMapping()
    {
        $this->self_channel->setIdsMap($this->shortLinks);
        $this->self_channel->setIdsMapReversed($this->shortBackLinks);

        $this->remote_channel->setIdsMap($this->shortBackLinks);
        $this->remote_channel->setIdsMapReversed($this->shortLinks);
    }

    protected function getLogger() {
        return Logger::getLogger('Commands');
    }

    private $object_it = null;
    private $remote_channel = null;
    private $self_channel = null;
    private $links = array();
    private $backlinks = array();
    private $shortLinks = array();
    private $shortBackLinks = array();
    private $mapping = array();
}
