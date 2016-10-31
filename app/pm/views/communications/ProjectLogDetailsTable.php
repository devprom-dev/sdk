<?php
include "ProjectLogDetailsList.php";

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
		return array(
			new ChangeLogActionFilter( $_REQUEST['action'] ),
			new ChangeLogStartFilter( getSession()->getLanguage()->getPhpDate(strtotime('-1 week', strtotime(date('Y-m-j')))) ),
			new ChangeLogVisibilityFilter()
		);
	}
}