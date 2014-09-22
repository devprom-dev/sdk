<?php

include "StateAttributeIterator.php";

class StateAttribute extends MetaobjectCacheable
{
    function __construct() 
 	{
 		parent::__construct('pm_StateAttribute');
 		
 		$this->setSortDefault( new SortAttributeClause('State') );
 	}
 	
 	function createIterator() 
 	{
 		return new StateAttributeIterator( $this );
 	}
 	
	function getDefaultAttributeValue( $name )
	{
		switch ( $name )
		{
			case 'Entity':
				return $this->getAttributeObject('State')->getObjectClass();
				
			default:
				return parent::getDefaultAttributeValue( $name );
		}
	}
}