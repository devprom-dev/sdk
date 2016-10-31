<?php
include "PlanChartDataRegistry.php";

class PlanChartData extends Metaobject
{
 	function __construct() 
 	{
 	    parent::__construct('entity', new PlanChartDataRegistry($this));
 	    $this->setSortDefault(
 	    		array (
 	            	new SortAttributeClause('StartDate')
 	    		)
 	    	);
 	}
}
