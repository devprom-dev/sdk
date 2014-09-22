<?php

include "StateBusinessRuleIterator.php";
include "StateBusinessRuleRegistry.php";
include_once "predicates/StateBusinessEntityFilter.php";

class StateBusinessRule extends Metaobject
{
 	function __construct( $entity = null ) 
 	{
 		parent::__construct('pm_Predicate', new StateBusinessRuleRegistry($this));
 	}
 	
 	function createIterator() 
 	{
 		return new StateBusinessRuleIterator( $this );
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