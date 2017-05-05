<?php

class TransitionAttributesPersister extends ObjectSQLPersister
{
    function getAttributes() {
        return array('LastTransition');
    }

    function getSelectColumns( $alias )
 	{
 		$columns = array();
 		
  		$objectPK = ($alias != '' ? $alias."." : "").'StateObject';
 		
 		array_push( $columns, 
 			"( SELECT so.Transition FROM pm_StateObject so ".
 			"   WHERE so.pm_StateObjectId = ".$objectPK." ) LastTransition " );

 		return $columns;
 	}

    function IsPersisterImportant() {
        return true;
    }
}