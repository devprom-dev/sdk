<?php
include "ProjectLogDetailsList.php";
include_once SERVER_ROOT_PATH . "pm/classes/communications/predicates/ChangeLogDocumentFilter.php";

class ProjectLogDetailsTable extends PMPageTable
{
	function getList() {
		return new ProjectLogDetailsList( $this->getObject() );
	}
	
	function getSortAttributeClause() {
        return new SortChangeLogRecentClause();
	}

	function getDefaultRowsOnPage() {
		return 10;
	}

	function getFilterPredicates()
	{
        $filters = array(
            new ChangeLogActionFilter( $_REQUEST['action'] ),
            new ChangeLogVisibilityFilter()
        );
        if ( $_REQUEST['document'] > 0 ) {
            $filters[] = new ChangeLogDocumentFilter($_REQUEST['document']);
            $filters[] = new ChangeLogStartFilter( getSession()->getLanguage()->getPhpDate(strtotime('-1 month', strtotime(date('Y-m-j')))) );
        }
        else {
            $filters[] = new ChangeLogStartFilter( getSession()->getLanguage()->getPhpDate(strtotime('-1 week', strtotime(date('Y-m-j')))) );
        }
        return $filters;
	}
}