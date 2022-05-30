<?php
include "IterationList.php";

class IterationTable extends PMPageTable
{
	function getList() {
		return new IterationList( $this->getObject() );
	}

    function getFilters()
    {
        $filters = array_merge(
            parent::getFilters(),
            array(
                $this->getObsoleteFilter()
            )
        );

        if ( $this->getObject()->hasAttribute('Version') ) {
            $filters[] = new FilterObjectMethod(getFactory()->getObject('Release'), '', 'release');
        }
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
        if ( $this->getObject()->hasAttribute('Version') ) {
            $filters[] = new FilterAttributePredicate('Version', $values['release']);
        }
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
            'releases',
            'milestones',
            'tasksplanningboard',
            'iterationplanningboard',
            'releaseplanningboard',
            'tasks-list',
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