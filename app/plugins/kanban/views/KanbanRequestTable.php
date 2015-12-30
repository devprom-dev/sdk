<?php
include_once SERVER_ROOT_PATH."plugins/kanban/classes/predicates/ProjectUseKanbanPredicate.php";
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
                new ProjectUseKanbanPredicate(),
                new FilterInPredicate($ids)
            )
        );
        $vpds = $project_it->fieldToArray('VPD');
        return count($vpds) > 0 ? $vpds : array(getSession()->getProjectIt()->get('VPD'));
    }

	protected function buildFilterState()
	{
		$filter = parent::buildFilterState();
		$filter->setDefaultValue('all');
		return $filter;
	}
}
