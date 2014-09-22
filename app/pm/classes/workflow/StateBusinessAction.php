<?php

include "StateBusinessActionIterator.php";
include "StateBusinessActionRegistry.php";
include_once "predicates/StateBusinessEntityFilter.php";

class StateBusinessAction extends Metaobject
{
 	function __construct() 
 	{
 		parent::__construct('pm_Predicate', new StateBusinessActionRegistry($this));
 	}
 	
 	function createIterator()
 	{
 		return new StateBusinessActionIterator( $this );
 	}

	function getExact( $id, $predicates = '' ) 
	{
	    $iterator = $this->getAll();
	    
		if ( !is_array($id) )
		{
		    $iterator->moveToId( $id );

		    return $iterator->getCurrentIt();
		}

		$id_key = $this->getClassName().'Id';
		
		$data = array();
		
		foreach( $iterator->getRowset() as $key => $value )
		{
			if ( in_array($value[$id_key], $id) ) $data[] = $value;
		}

		return $this->createCachedIterator( $data );
	}
}