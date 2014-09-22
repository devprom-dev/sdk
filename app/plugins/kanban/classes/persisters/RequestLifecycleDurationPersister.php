<?php

class RequestLifecycleDurationPersister extends ObjectSQLPersister
{
 	function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
 		$alias = $alias != '' ? $alias."." : "";
 		
 		$columns[] = " (SELECT t.LifecycleDuration / 24 ) LifecycleDuration ";  
		
 		return $columns;
 	}
}