<?php
include "ReleaseList.php";

class ReleaseTable extends PMPageTable
{
	function getList() {
		return new ReleaseList( $this->getObject() );
	}

    function getFilters()
    {
        $filters = array_merge(
            parent::getFilters(),
            array(
                $this->getObsoleteFilter()
            )
        );
        return $filters;
    }

    protected function getObsoleteFilter()
    {
        $filter = new FilterCheckMethod(
            $this->getObject()->getAttributeUserName('IsClosed'), 'closed'
        );
        $filter->setDefaultValue('N');
        return $filter;
    }

    function getFilterPredicates( $values )
    {
        $filters = array(
            new FilterAttributePredicate('IsClosed', $values['closed']),
        );
        return array_merge(
            parent::getFilterPredicates( $values ),
            $filters
        );
    }

    protected function getFamilyModules( $module )
    {
        return array (
            'delivery',
            'project-plan-hierarchy',
            'iterations',
            'milestones',
            'tasksplanningboard',
            'iterationplanningboard',
            'releaseplanningboard',
            'assignedtasks',
            'assignedissues',
            'tasksbyassignee',
            'projects',
            'process/metrics'
        );
    }

    protected function getChartModules( $module )
    {
        return array (
            'resman/resourceload',
            'projectburnup',
            'iterationburndown',
            'scrum/velocitychart',
            'resman/resourceload'
        );
    }
}