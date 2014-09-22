<?php

class TaskFactPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		$alias = $alias != '' ? $alias."." : "";
 		
		$object = $this->getObject();
  		$objectPK = $alias.$object->getClassName().'Id';
 		
 		array_push( $columns, 
 			"(SELECT ROUND(SUM(ac.Capacity),1) FROM pm_Activity ac ".
 			"  WHERE ac.Task = ".$objectPK." ) Fact " );

 		return $columns;
 	}
}
