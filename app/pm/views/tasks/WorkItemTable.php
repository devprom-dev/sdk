<?php
include "WorkItemList.php";
include "WorkItemChart.php";

class WorkItemTable extends TaskTable
{
    function __construct( $object )
    {
        $object->setAttributeCaption('IssueTraces', text(922));
        parent::__construct($object);
    }

    function getList( $mode = '' )
    {
        switch( $this->getReportBase() )
        {
            case 'workitemchart':
                return new WorkItemChart($this->getObject());
            default:
                return new WorkItemList($this->getObject());
        }
    }

    function getBulkActions() {
        return array();
    }

    function getNewActions()
    {
        $append_actions = array();

        if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
        {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Task'));
            if ( $method->hasAccess() ) {
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
            if ( $method->hasAccess() ) {
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

    protected function buildFilterState( $filterValues = array() )
    {
        $filter = new FilterObjectMethod(getFactory()->getObject('WorkItemState')->getAll(), translate('Состояние'), 'state');
        $filter->setDefaultValue('initial,progress');
        $filter->setHasNone(false);
        $filter->setIdFieldName('ReferenceName');
        return $filter;
    }

    function buildStatePredicate( $value ) {
        return new WorkItemStatePredicate( $value );
    }

    function buildIssueStatePredicate( $values ) {
        return new WorkItemExactTypeStatePredicate($values['issueState'], getFactory()->getObject('Request'));
    }

    function buildAssigneeFilter()
    {
        if ( $this->getReportBase() == 'mytasks' ) return null;
        return parent::buildAssigneeFilter();
    }

    function buildAssigneePredicate( $values ) {
        if ( $this->getReportBase() == 'mytasks' ) {
            return new FilterAttributePredicate('Assignee', getSession()->getUserIt()->getId());
        }
        else {
            return parent::buildAssigneePredicate($values);
        }
    }

    function buildDeadlinePredicate( $values ) {
        return new FilterDateBeforePredicate('DueDate', $values['plannedfinish']);
    }

    function getDefaultRowsOnPage() {
        return 20;
    }

    function getChartModules($module)
    {
        return array_merge(
            array(
                'workitemchart'
            ),
            parent::getChartModules($module)
        );
    }

    function getDetailsParms() {
        return array (
            'active' => 'form'
        );
    }
}
