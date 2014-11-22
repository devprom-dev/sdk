<?php

include "KanbanRequestBoard.php";

class KanbanRequestTable extends RequestTable
{
 	function getList( $mode = '' )
 	{
 		if ( $mode == 'board' )
 		{
 			return new KanbanRequestBoard( $this->getObject() );
 		}
 		
 		return parent::getList( $mode );
 	}
 	
 	function getFiltersDefault()
 	{
 	    return array();
 	}
 	
	protected function buildFilterState()
	{
		$filter = parent::buildFilterState();
		
		$filter->setDefaultValue('all');
		
		return $filter;
	}
}
