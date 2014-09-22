<?php

class TaskTraceList extends TaskList
{
 	var $iterator;
 	
 	function TaskTraceList( $object )
 	{
 		parent::TaskList( $object );
 	}
 	
 	function getItemActions( $caption, $object_it )
 	{
 	    return array();
 	}
	
	function getColumnFields()
	{
		return PageList::getColumnFields();
	}
	
	function getColumnWidth( $attr ) 
	{
		if ( $attr != 'Caption' && $attr != 'UID' ) return '8%';
			
		return parent::getColumnWidth( $attr );
	}
}