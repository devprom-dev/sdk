<?php

include "MilestoneList.php";

class MilestoneTable extends PMPageTable
{
	function getList()
	{
		return new MilestoneList( $this->object );
	}

	function getFilters()
	{
		return array_merge( parent::getFilters(), array( new MilestoneFilterStateWebMethod() ));
	}
	
	function getFilterPredicates()
	{
	    $values = $this->getFilterValues();
	    
	    return array_merge( parent::getFilterPredicates(), array( 
	            new FilterAttributePredicate('Passed', $values['state']) 
	    ));
	}
} 