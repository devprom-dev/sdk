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

    protected function buildStateFilter() {
        return new StateExFilterWebMethod(getFactory()->getObject('WorkItemState')->getAll(), 'taskstate');
    }

    function buildStatePredicate( $value ) {
        return new WorkItemStatePredicate( $value );
    }

    function getDefaultRowsOnPage() {
        return 20;
    }
}
