<?php

class TaskDatesPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('DueWeeks');
    }

    function getSelectColumns( $alias )
 	{
 		 $columns = array();

		$columns[] =
			"  		IFNULL( t.PlannedFinishDate, ".
			"				IFNULL( ".
			"						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
			"						NULL ".
			"				) ".
			"			) DueDate ";

         $columns[] =
         	 "  IFNULL(LEAST(5, YEARWEEK(IFNULL( t.FinishDate, ".
         	 "  		IFNULL( t.PlannedFinishDate, ".
         	 "				IFNULL( ".
         	 "						(SELECT i.FinishDate FROM pm_Release i WHERE i.pm_ReleaseId = t.Release), ".
             "						NULL ".
             "				) ".
         	 "			) ".
             "  	)  ".
             "	) - YEARWEEK(IFNULL(t.FinishDate,NOW()))) + 2, 7) DueWeeks ";

 		return $columns;
 	}

 	function map( &$parms )
    {
        if ( !array_key_exists('DueWeeks', $parms) ) return;

        $value_it = getFactory()->getObject('DeadlineSwimlane')->getExact($parms['DueWeeks']);
        if ( $value_it->getId() == '' ) return;

        $parms['PlannedFinishDate'] = $value_it->get('ReferenceName');
    }
}
