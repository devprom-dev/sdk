<?php
include "DeliveryChartDataRegistry.php";
include "predicates/DeliveryPriorityPredicate.php";
include "predicates/DeliveryProductTypePredicate.php";
include "predicates/DeliveryImportancePredicate.php";
include "predicates/DeliveryStatePredicate.php";
include "predicates/DeliveryStartAfterPredicate.php";
include "predicates/DeliveryStartBeforePredicate.php";

class DeliveryChartData extends Metaobject
{
 	function __construct() 
 	{
 	    parent::__construct('entity', new DeliveryChartDataRegistry($this));

 	    $this->addAttribute('FinishDate', 'DATE', '', false);
        $this->addAttribute('Project', 'INTEGER', '', false);
        $this->addAttribute('SortIndex', 'INTEGER', '', false);
 	    $this->setSortDefault(
 	    		array (
 	            	new SortAttributeClause('FinishDate')
 	    		)
 	    	);
 	}
}
