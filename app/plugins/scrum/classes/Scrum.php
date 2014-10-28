<?php

include "ScrumIterator.php";
include "ScrumGrouppedRegistry.php";

class Scrum extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::Metaobject('pm_Scrum', $registry);
 		
		$this->setSortDefault( new SortAttributeClause('RecordCreated.D') );
 	}
 	
 	function createIterator() 
 	{
 		return new ScrumIterator( $this );
 	}
 	
 	function getAttributeUserName( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'GroupDate':
 				return translate('Дата');
 				
 			default:
 				return parent::getAttributeUserName( $attr );
 		}
 	}

 	function getDefaultAttributeValue( $name )
	{
		global $part_it;
		
		if( $name == 'Participant' )
		{
			return $part_it->getId();
		}
		
		return parent::getDefaultAttributeValue( $name );
	}
}
