<?php
include "WorkItemList.php";

class WorkItemTable extends TaskTable
{
    function __construct( $object )
    {
        $object->setAttributeCaption('IssueTraces', text(922));
        parent::__construct($object);
    }

    function getList( $mode = '' ) {
        return new WorkItemList($this->getObject());
    }

    function getBulkActions() {
        return array();
    }

    function getNewActions() {
        return array();
    }

    function getImportActions() {
        return array();
    }

    function getFilterPredicates($values)
    {
        return array_merge(
            parent::getFilterPredicates($values),
            array(
                $this->buildTaskTypePredicate($values['tasktype'])
            )
        );
    }

    function buildTagPredicate($values) {
        return new WorkItemTagFilter($values['tag']);
    }

    protected function buildTagsFilter()
    {
        $tag = getFactory()->getObject('Tag');
        $filter = new FilterObjectMethod($tag, translate('Тэги'), 'tag');
        $filter->setHasAll(false);
        $filter->setHasNone(false);
        return $filter;
    }

    protected function buildTypeFilter()
    {
        $type_method = new FilterObjectMethod( getFactory()->getObject('WorkItemType'), translate('Тип'), 'tasktype');
        $type_method->setType('singlevalue');
        $type_method->setHasAny(false);
        $type_method->setHasNone(false);
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

    function buildTaskTypePredicate( $value ) {
        return new FilterTextExactPredicate('TaskType', $value );
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

    function getSortAttributeClause( $field )
    {
        $parts = preg_split('/\./', $field);

        if ( $parts[0] == 'TaskType' ) {
            return new WorkItemTypeSortClause();
        }

        return parent::getSortAttributeClause( $field );
    }

    function getDefaultRowsOnPage() {
        return 20;
    }

    function getChartModules($module)
    {
        return array_merge(
            array(
                'resman/resourceload'
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
