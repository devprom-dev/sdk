<?php
include "WorkloadDetailsList.php";

class WorkloadDetailsTable extends PMPageTable
{
	function getList() {
		return new WorkloadDetailsList( $this->getObject() );
	}
	
	function getSortDefault( $sort_parm = 'sort' )
	{
		if ( $sort_parm == 'sort' ) {
			return 'Caption';
		}
		return parent::getSortDefault( $sort_parm );
	}

	function getFilterPredicates()
	{
		return array(
			new FilterInPredicate($_REQUEST['users'])
		);
	}
}