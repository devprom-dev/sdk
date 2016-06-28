<?php
include "ProjectLogDetailsList.php";

class ProjectLogDetailsTable extends PMPageTable
{
	function getList() {
		return new ProjectLogDetailsList( $this->getObject() );
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' ) {
			return 'ChangeDate.D';
		}
		if ( $sort_parm == 'sort2' ) {
			return 'RecordModified.D';
		}
		return parent::getSortDefault( $sort_parm );
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