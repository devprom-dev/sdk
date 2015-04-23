<?php

class RequestEstimatesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] =
 	 		"(SELECT ROUND(SUM(s.Planned),1) FROM pm_Task s WHERE s.ChangeRequest = ".$this->getPK($alias).") TasksPlanned ";
 		
 		return $columns;
 	}
}
