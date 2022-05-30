<?php
namespace Devprom\ProjectBundle\Service\Task;

use PhpImap\Exception;

class TaskConvertToIssueService
{
    private $factory = null;
    private $targetClassName = null;

    function __construct( \ModelFactory $factory, $targetClassName = 'Request' ) {
        $this->factory = $factory;
        $this->targetClassName = $targetClassName;
    }

    function convert( \TaskIterator $taskIt )
    {
        $result = array();
        while( !$taskIt->end() ) {
            $requestIt = $this->mapTaskToIssue($taskIt);
            if ( $requestIt->getId() == '' ) {
                throw new Exception('Unable create issue based on a task');
            }
            $result[] = $requestIt->getData();

            $this->bindAttachments($taskIt, $requestIt);
            $this->bindComments($taskIt, $requestIt);
            $this->bindActivities($taskIt, $requestIt);
            $this->bindWatchers($taskIt, $requestIt);
            $this->convertTraces($taskIt, $requestIt);

            $taskIt->object->getRegistry()->Delete($taskIt);
            $taskIt->moveNext();
        }
        return $this->factory->getObject('Request')->createCachedIterator($result);
    }

    function bindComments($taskIt, $requestIt)
    {
        $comment_it = $this->factory->getObject('Comment')->getRegistry()->Query(
            array(
                new \FilterAttributePredicate('ObjectClass', get_class($taskIt->object)),
                new \FilterAttributePredicate('ObjectId', $taskIt->getId())
            )
        );
        while( !$comment_it->end() ) {
            $comment_it->object->getRegistry()->Store($comment_it,
                array(
                    'ObjectClass' => 'Request',
                    'ObjectId' => $requestIt->getId()
                )
            );
            $comment_it->moveNext();
        }
    }

    function bindActivities($taskIt, $requestIt)
    {
        $activity = $this->factory->getObject('ActivityRequest');
        $activity_it = $this->factory->getObject('ActivityTask')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('Task', $taskIt->getId())
            )
        );
        while( !$activity_it->end() ) {
            $data = $activity_it->getData();
            unset($data[$activity_it->object->getIdAttribute()]);

            $activity->getRegistry()->Create(
                array_merge( $data,
                    array(
                        'Task' => $requestIt->getId()
                    )
                )
            );
            $activity_it->moveNext();
        }
    }

    function bindWatchers($taskIt, $requestIt)
    {
        $object_it = $this->factory->getObject('Watcher')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('ObjectClass', $taskIt->object->getEntityRefName()),
                new \FilterAttributePredicate('ObjectId', $taskIt->getId())
            )
        );
        while( !$object_it->end() ) {
            $object_it->object->getRegistry()->Store($object_it,
                array(
                    'ObjectClass' => $requestIt->object->getEntityRefName(),
                    'ObjectId' => $requestIt->getId()
                )
            );
            $object_it->moveNext();
        }
    }

    function bindAttachments($taskIt, $requestIt)
    {
        $object_it = $this->factory->getObject('Attachment')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('ObjectClass', strtolower(get_class($taskIt->object))),
                new \FilterAttributePredicate('ObjectId', $taskIt->getId())
            )
        );
        while( !$object_it->end() ) {
            $object_it->object->getRegistry()->Store($object_it,
                array(
                    'ObjectClass' => strtolower(get_class($requestIt->object)),
                    'ObjectId' => $requestIt->getId()
                )
            );
            $object_it->moveNext();
        }
    }

    function convertTraces($taskIt, $requestIt)
    {
        $trace = $this->factory->getObject('RequestTraceBase');
        $trace_it = $this->factory->getObject('TaskTraceBase')->getRegistry()->Query(
            array (
                new \FilterAttributePredicate('Task', $taskIt->getId())
            )
        );
        while( !$trace_it->end() ) {
            $data = $trace_it->getData();
            unset($data['Task']);
            unset($data[$trace_it->object->getIdAttribute()]);

            $trace->getRegistry()->Create(
                array_merge( $data,
                    array(
                        'ChangeRequest' => $requestIt->getId(),
                        'Type' => REQUEST_TRACE_PRODUCT
                    )
                )
            );
            $trace_it->moveNext();
        }
    }

    function mapTaskToIssue( \TaskIterator $task_it )
    {
        $parms = $this->mapToIssue($task_it->getData());
        foreach( $task_it->object->getAttributes() as $key => $info ) {
            if ( !$task_it->object->IsAttributeStored($key) ) {
                unset($parms[$key]);
            }
        }
        if ( $task_it->object->getAttributeType('Description') == '' ) {
            foreach( $task_it->object->getAttributes() as $key => $info ) {
                if ( $task_it->object->getAttributeType($key) == 'wysiwyg' ) {
                    $parms['Description'] = $task_it->getHtmlDecoded($key);
                    break;
                }
            }
        }
        return $this->factory->getObject($this->targetClassName)->getRegistry()->Create($parms);
    }

    function mapToIssue( $parms )
    {
        $mapping = array (
            'Assignee' => 'Owner',
            'Release' => 'Iteration',
            'Planned' => 'Estimation'
        );
        foreach( $parms as $key => $value ) {
            if ( $mapping[$key] != '' ) {
                $parms[$mapping[$key]] = \TextUtils::decodeHtml($value);
            }
            else {
                $parms[$key] = \TextUtils::decodeHtml($value);
            }
        }
        unset($parms['State']);
        return $parms;
    }
}