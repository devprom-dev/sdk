<?php
use Devprom\ProjectBundle\Service\Model\ModelService;

class IntegrationDevpromChannel extends IntegrationChannel
{
    public function __construct($object_it, $logger)
    {
        parent::__construct($object_it, $logger);
        $this->model = new ModelService(
            new \ModelValidator(
                array (
                    new \ModelValidatorObligatory(),
                    new \ModelValidatorTypes()
                )
            ),
            new \ModelDataTypeMapper(),
            array(),
            null,
            true
        );
        $this->model->setSkipFields(array('VPD','RecordVersion'));
    }

    public function getKeyField() {
        return 'Id';
    }

    public function getItems( $timestamp, $limit )
    {
        $registry = new \ChangeLogGranularityRegistry();
        $registry->setGranularity(\ChangeLogGranularityRegistry::HOUR);
        $registry->setLimit($limit);

        if ( $timestamp == '' ) {
            $timestamp = strftime('%Y-%m-%d %H:%M:%S', strtotime('-1 day', strtotime(SystemDateTime::date())));
        }
        $this->getLogger()->info('Looking for changes since '.$timestamp);

        $registry->setObject(new \ChangeLog());
        $log_it = $registry->Query(
            array (
                new FilterVpdPredicate(),
                new ChangeLogStartServerFilter($timestamp),
                new \SortAttributeClause('ObjectChangeLogId.A')
            )
        );

        $connectorName = $this->getObjectIt()->getDisplayName();
        $mapping = $this->getMapping();
        $attachment = getFactory()->getObject('Attachment');
        $requestLink = getFactory()->getObject('RequestLink');
        $activity = getFactory()->getObject('Activity');

        $items = array();
        while( !$log_it->end() )
        {
            $class = getFactory()->getClass($log_it->get('ClassName'));
            if ( !class_exists($class) || $class == 'metaobject' ) {
                $log_it->moveNext();
                continue;
            }

            $object = getFactory()->getObject($class);
            $class = get_class($object);
            if ( !is_array($mapping[$class]) ) {
                $log_it->moveNext();
                continue;
            }

            if ( $log_it->get('ChangeKind') != 'deleted' ) {
                $object_it = $object->getExact($log_it->get('ObjectId'));
                if ( $object_it->getId() == '' ) {
                    // skip non-existent item
                    $log_it->moveNext();
                    continue;
                }
            }

            if ( $log_it->get('Author') == $connectorName ) {
                // skip item created by the connector
                $log_it->moveNext();
                continue;
            }

            $key = $class.$log_it->get('ObjectId');

            if ( $log_it->get('ChangeKind') == 'commented' ) {
                preg_match('/O-(\d+)/', $log_it->get('Content'), $matches);
                $items[$key.'Comment'] = array (
                    'class' => get_class(getFactory()->getObject($class)).'Comment',
                    'id' => $matches[1],
                    'action' => 'add',
                    'parentId' => $log_it->get('ObjectId'),
                    'author' => $log_it->getRef('SystemUser')->get('Email')
                );
                $log_it->moveNext();
                continue;
            }

            $items[$key] = array (
                'class' => $class,
                'id' => $log_it->get('ObjectId'),
                'action' => $log_it->get('ChangeKind') == 'deleted'
                                ? 'delete'
                                : ($log_it->get('ChangeKind') == 'added' ? 'add' : 'update'),
                'attributes' => preg_split('/,/',$log_it->get('Attributes')),
                'author' => $log_it->getRef('SystemUser')->get('Email')
            );

            if ( $items[$key]['id'] < 1 ) {
                $log_it->moveNext();
                continue;
            }

            $attachment_it = $attachment->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ObjectId', $items[$key]['id']),
                    new FilterAttributePredicate('ObjectClass', strtolower($items[$key]['class'])),
                    new FilterModifiedAfterPredicate($timestamp)
                )
            );
            while( !$attachment_it->end() ) {
                $items[] = array (
                    'class' => $items[$key]['class'].'Attachment',
                    'id' => $attachment_it->getId(),
                    'parentId' => $attachment_it->get('ObjectId')
                );
                $attachment_it->moveNext();
            }

            if ( $class == 'Request' ) {
                $link_it = $requestLink->getRegistry()->Query(
                    array (
                        new FilterAttributePredicate('SourceRequest', $items[$key]['id']),
                        new FilterModifiedAfterPredicate($timestamp)
                    )
                );
                while( !$link_it->end() ) {
                    $items[] = array (
                        'class' => 'RequestLink',
                        'id' => $link_it->getId(),
                        'parentId' => $link_it->get('SourceRequest')
                    );
                    $link_it->moveNext();
                }
                $it = $activity->getRegistry()->Query(
                    array (
                        new FilterAttributePredicate('Issue', $items[$key]['id']),
                        new FilterModifiedAfterPredicate($timestamp)
                    )
                );
                while( !$it->end() ) {
                    $items[] = array (
                        'class' => 'ActivityRequest',
                        'id' => $it->getId(),
                        'parentId' => $it->get('Issue')
                    );
                    $it->moveNext();
                }
            }

            if ( $class == 'Task' ) {
                $it = $activity->getRegistry()->Query(
                    array (
                        new FilterAttributePredicate('Task', $items[$key]['id']),
                        new FilterModifiedAfterPredicate($timestamp)
                    )
                );
                while( !$it->end() ) {
                    $items[] = array (
                        'class' => 'ActivityTask',
                        'id' => $it->getId(),
                        'parentId' => $it->get('Task')
                    );
                    $it->moveNext();
                }
            }

            $log_it->moveNext();
        }

        return $items;
    }

    public function readItem($mapping, $class, $id, $parms = array())
    {
        try {
            $result = $this->model->get($class, $id, 'text');
            $result['SourceId'] = $class.$id;
            return $result;
        }
        catch (\Exception $e) {
            $this->getLogger()->error($e->getMessage().PHP_EOL.$e->getTraceAsString());
            return array (
                'Id' => $id
            );
        }
    }

    public function writeItem($mapping, $class, $id, $data, $queueItem)
    {
        $this->getLogger()->debug('Devprom writeItem: '.var_export($data,true));

        $result = $this->model->set($class, $data, $id);
        if ( $id == $result['Id'] ) {
            $this->getLogger()->info('Item has been updated: '.$result['Id']);
        }
        else {
            $this->getLogger()->info('Item has been created: '.$result['Id']);
        }

        return array($result);
    }

    public function deleteItem($mapping, $class, $id)
    {
    }

    public function storeLink( $mapping, $class, $id, $link, $title )
    {
    }

    public function buildDictionaries()
    {
    }

    private $model = null;
}