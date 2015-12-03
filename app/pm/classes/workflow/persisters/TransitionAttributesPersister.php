<?php

class TransitionAttributesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$objectPK = ($alias != '' ? $alias."." : "").'StateObject';
 		
 		array_push( $columns, 
 			"( SELECT so.Transition FROM pm_StateObject so ".
 			"   WHERE so.pm_StateObjectId = ".$objectPK." ) LastTransition " );

 		return $columns;
 	}
}