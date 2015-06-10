<?php

class RequestTraceList extends RequestList
{
 	function __construct( $object )
 	{
 		parent::RequestList( $object );
 	}
 	
	function IsNeedToDisplayOperations() { return false; } 

  	function getItemActions( $caption, $object_it )
 	{
 	    return array();
 	}
	
	function getColumnWidth( $attr ) 
	{
		if ( $attr == 'Tasks' )
		{
			return '8%';
		}
		
		if ( $attr != 'Caption' && $attr != 'UID' )
			return '8%';
			
		return parent::getColumnWidth( $attr );
	}
}