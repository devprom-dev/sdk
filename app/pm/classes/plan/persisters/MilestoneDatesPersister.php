<?php

class MilestoneDatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 	    $columns = array();
 	    
 		$columns[] =  
 			"(SELECT TO_DAYS(NOW()) - TO_DAYS(MilestoneDate)) Overdue ";
        $columns[] =
            " YEARWEEK(t.MilestoneDate) - YEARWEEK(NOW()) DueWeeks ";

 		return $columns;
 	}
}
