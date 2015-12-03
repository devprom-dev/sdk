<?php
include "WorkItemList.php";

class WorkItemTable extends TaskTable
{
    function __construct( $object )
    {
        $object->setAttributeCaption('IssueTraces', text(922));
        parent::__construct($object);
    }

    function getList( $mode = '' )
    {
        return new WorkItemList($this->getObject());
    }

    function getBulkActions()
    {
        return array();
    }

    function getNewActions()
    {
        $append_actions = array();

        if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
        {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Task'));
            if ( $method->hasAccess() )
            {
                $method->setRedirectUrl('donothing');
                $parms = array (
                    'area' => $this->getPage()->getArea(),
                    'Assignee' => getSession()->getUserIt()->getId()
                );

                $uid = 'append-task';
                $append_actions[$uid] = array (
                    'name' => $method->getObject()->getDisplayName(),
                    'uid' => $uid,
                    'url' => $method->getJSCall($parms)
                );
            }
        }
        else
        {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Request'));
            if ( $method->hasAccess() )
            {
                $method->setRedirectUrl('donothing');
                $parms = array (
                    'area' => $this->getPage()->getArea(),
                    'Owner' => getSession()->getUserIt()->getId()
                );

                $uid = 'append-issue';
                $append_actions[$uid] = array (
                    'name' => $method->getObject()->getDisplayName(),
                    'uid' => $uid,
                    'url' => $method->getJSCall($parms)
                );
            }
        }

        return $append_actions;
    }
    protected function buildTypeFilter()
    {
        $type_method = new FilterObjectMethod( getFactory()->getObject('WorkItemType'), translate('Тип'), 'tasktype');
        $type_method->setIdFieldName( 'ReferenceName' );
        return $type_method;
    }

    protected function buildStateFilter()
    {
        $state_objects = array(getFactory()->getObject('IssueState'));
        if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) {
            $state_objects[] = getFactory()->getObject('TaskState');
        }
        if ( count($state_objects) > 1 ) {
            $metastate = getFactory()->getObject('StateMeta');
            $metastate->setAggregatedStateObject($state_objects);
            $state_it = $metastate->getRegistry()->getAll();
        }
        else {
            $object = array_pop($state_objects);
            $state_it = $object->getAll();
        }
        return new StateExFilterWebMethod($state_it, 'taskstate');
    }
}
