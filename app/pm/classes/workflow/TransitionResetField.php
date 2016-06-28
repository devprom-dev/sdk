<?php

include "TransitionResetFieldIterator.php";

class TransitionResetField extends Metaobject
{
 	function __construct()
 	{
 		parent::__construct('pm_TransitionResetField');
 		$this->setSortDefault( new SortAttributeClause('Transition') );
 	}
 	
 	function createIterator() 
 	{
 		return new TransitionResetFieldIterator( $this );
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
