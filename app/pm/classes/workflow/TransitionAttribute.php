<?php

include "TransitionAttributeIterator.php";
include "predicates/TransitionAttributeEntityAttributesPredicate.php";
include "sorts/TransitionAttributeSortClause.php";

class TransitionAttribute extends Metaobject
{
    var $state_it;
    
    function __construct() 
 	{
 		parent::__construct('pm_TransitionAttribute');
 		$this->setSortDefault( new TransitionAttributeSortClause() );
 	}
 	
 	function createIterator() 
 	{
 		return new TransitionAttributeIterator( $this );
 	}
 	
 	function setStateIt( $state_it )
 	{
 	    $this->state_it = $state_it;
 	}
 	
	function getDefaultAttributeValue( $name )
	{
		switch ( $name )
		{
			case 'Entity':
				return is_object($this->state_it) 
				    ? $this->state_it->get('ObjectClass') : '';
				
			default:
				return parent::getDefaultAttributeValue( $name );
		}
	}
}