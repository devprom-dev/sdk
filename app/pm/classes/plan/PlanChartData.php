<?php
include "PlanChartDataRegistry.php";

class PlanChartData extends Metaobject
{
 	function __construct() 
 	{
 	    parent::__construct('entity', new PlanChartDataRegistry($this));

        $this->addAttribute('StartDate', 'VARCHAR', '', false);
        $this->addAttribute('FinishDate', 'VARCHAR', '', false);
        $this->addAttribute('Project', 'VARCHAR', '', false);
        $this->addAttribute('SortIndex', 'VARCHAR', '', false);
        $this->addAttribute('ObjectClass', 'VARCHAR', '', false);
        foreach( array('StartDate','FinishDate','Project','SortIndex') as $attribute ) {
            $this->setAttributeGroups($attribute, array('system'));
        }
 	    $this->setSortDefault(
 	    		array (
 	            	new SortAttributeClause('StartDate')
 	    		)
 	    	);
 	}

    function getVpdValue() {
        return '';
    }
}
