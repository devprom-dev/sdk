<?php

class StateBaseModelPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$columns[] = 
 			" ( SELECT GROUP_CONCAT(CAST(a.pm_StateActionId AS CHAR)) ".
 			"	  FROM pm_StateAction a WHERE a.State = ".$this->getPK($alias)." ) Actions ";
 		
 		$columns[] = 
 			" ( SELECT GROUP_CONCAT(CAST(a.pm_StateAttributeId AS CHAR)) ".
 			"	  FROM pm_StateAttribute a WHERE a.State = ".$this->getPK($alias)." ) Attributes ";
 		
 		return $columns;
 	}
}