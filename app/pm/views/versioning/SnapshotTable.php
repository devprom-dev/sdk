<?php

include "SnapshotList.php";

class SnapshotTable extends PMPageTable
{
	function getList()
	{
		return new SnapshotList( $this->object );
	}

	function IsNeedToAdd() 
	{
		return false;
	}
	
 	function getFilterOrientation()
 	{
 		return 'left';
 	}
 	
 	function getFilterPredicates()
 	{
 		$values = $this->getFilterValues();

 		return array_merge( parent::getFilterPredicates(), array (
 				new FilterAttributePredicate('ObjectClass', $_REQUEST['object'])
 		));
 	}
} 
