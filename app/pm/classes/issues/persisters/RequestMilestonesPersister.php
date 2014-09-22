<?php

class RequestMilestonesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =  
 			"( SELECT GROUP_CONCAT(CAST(tr.ObjectId AS CHAR)) ".
 			"    FROM pm_ChangeRequestTrace tr " .
			"   WHERE tr.ChangeRequest = ".$this->getPK($alias).
 			"     AND tr.ObjectClass = 'Milestone' ) Deadlines ";
  		
 		$columns[] =  
 			"( SELECT GROUP_CONCAT(m.MilestoneDate) ".
 			"    FROM pm_ChangeRequestTrace tr, pm_Milestone m " .
			"   WHERE tr.ChangeRequest = ".$this->getPK($alias).
			"     AND tr.ObjectId = m.pm_MilestoneId ".
 			"     AND tr.ObjectClass = 'Milestone' ) DeadlinesDate ";

 		return $columns;
 	}
 	
 	function modify( $object_id, $parms )
 	{
 		if ( $parms['Deadlines'] == '' ) return;
 		
 		$trace = getFactory()->getObject('RequestTraceMilestone');
 		
 		$trace_it = $trace->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('ChangeRequest', $object_id)
 				)
 		); 
 		
 		while( !$trace_it->end() )
 		{
 			$trace_it->delete();
 			$trace_it->moveNext();
 		}

 		$ids = array_filter(preg_split('/,/', $parms['Deadlines']), function($value) {
 				return $value > 0;
 		});
 		
 		foreach( $ids as $milestone_id )
 		{
 			$trace->add_parms(
 					array (
 							'ChangeRequest' => $object_id,
 							'ObjectId' => $milestone_id,
 							'ObjectClass' => 'Milestone'
 					)
 			);
 		}
 	}
}
