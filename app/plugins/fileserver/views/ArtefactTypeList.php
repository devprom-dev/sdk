<?php

class ArtefactTypeList extends PageList
{
	function IsNeedToDisplay( $attr ) 
	{
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