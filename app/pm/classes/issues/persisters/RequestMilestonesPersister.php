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
  		
 		return $columns;
 	}
}
