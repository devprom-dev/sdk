<?php

class TransitionAttributesPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$objectPK = ($alias != '' ? $alias."." : "").'StateObject';
 		
 		array_push( $columns, 
 			"( SELECT GROUP_CONCAT(CAST(so.Transition AS CHAR)) FROM pm_StateObject so ".
 			"   WHERE so.ObjectId = ".$this->getPK($alias)." AND so.ObjectClass = '".$this->getObject()->getStatableClassName()."' ) Transition " );

 		array_push( $columns, 
 			"( SELECT so.Comment FROM pm_StateObject so ".
 			"   WHERE so.pm_StateObjectId = ".$objectPK." ) TransitionComment " );
 		
 		return $columns;
 	}
}