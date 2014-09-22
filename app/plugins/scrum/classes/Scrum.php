<?php

include "ScrumIterator.php";

class Scrum extends Metaobject
{
 	function __construct() 
 	{
 		parent::Metaobject('pm_Scrum');
 		
		$this->setSortDefault( new SortAttributeClause('RecordCreated.D') );
 	}
 	
 	function createIterator() 
 	{
 		return new ScrumIterator( $this );
 	}
 	
 	function getGroupedByDay() 
 	{
 		$sort = $this->getSortClause();
 		
 		$sql = "SELECT t.*, DATE_FORMAT(t.RecordCreated, '".getSession()->getLanguage()->getDateFormat()."') GroupDate" .
 			   "  FROM pm_Scrum t " .
 			   " WHERE t.VPD IN ('".join("','",$this->getVpds())."') ".
 			   " ORDER BY ".($sort != '' ? $sort."," : "")." t.RecordCreated DESC ";
 		
 		return $this->createSQLIterator($sql);
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
