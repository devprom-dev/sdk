<?php
include "KanbanRequestBoard.php";

class KanbanRequestTable extends RequestTable
{
    private $vpds = array();

    function __construct($object)
    {
        parent::__construct($object);
        $this->vpds = $this->buildProjectVpds();
    }

    function getProjectVpds() {
        return $this->vpds;
    }

    function getList( $mode = '' ) {
		return new KanbanRequestBoard( $this->getObject() );
 	}
 	
 	function getFiltersDefault() {
 	    return array('type','priority');
 	}

	function hasCrossProjectFilter() {
		return count($this->vpds) > 1;
	}

    function getFilterPredicates() {
        return array_merge(
            parent::getFilterPredicates(),
            array (
                new FilterVpdPredicate($this->vpds)
            )
        );
    }

    protected function buildProjectVpds()
    {
        $ids = getSession()->getProjectIt()->IsPortfolio() || getSession()->getProjectIt()->IsProgram()
            ? getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('pm_ProjectId')
            : array();

        if ( !getSession()->getProjectIt()->IsPortfolio() ) {
            $ids[] = getSession()->getProjectIt()->getId();
        }
        $project_it = getFactory()->getObject('Project')->getRegistry()->Query(
            array (
                new FilterAttributePredicate('Tools', array('kanban_ru.xml','kanban_en.xml')),
                new FilterInPredicate($ids)
            )
        );
        return $project_it->fieldToArray('VPD');
    }

	protected function buildFilterState()
	{
		$filter = parent::buildFilterState();
		$filter->setDefaultValue('all');
		return $filter;
	}
}
