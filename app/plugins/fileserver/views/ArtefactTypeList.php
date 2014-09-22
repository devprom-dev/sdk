<?php

class ArtefactTypeList extends PageList
{
 	function getIterator() 
 	{
		return $this->getObject()->getAll();
	}
	
	function IsNeedToDisplay( $attr ) 
	{
		global $project_it;
		
 		switch( $attr ) 
 		{
 			case 'Caption':
 				return true;
 				
 			default:
 				return parent::IsNeedToDisplay( $attr );
 		}
	}
	
	function IsNeedToDisplayLinks( ) 
	{ 
		return false; 
	}
}