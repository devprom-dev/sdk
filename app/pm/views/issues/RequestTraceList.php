<?php

class RequestTraceList extends RequestList
{
 	function __construct( $object )
 	{
 		parent::RequestList( $object );
 	}
 	
	function IsNeedToDisplayOperations() { return false; } 

	function setupColumns()
	{
		$visible = array_merge( 
				array(
						'UID', 
						'Caption'
				), 
		    	$this->getObject()->getAttributesByGroup('trace')
		);
		
		parent::setupColumns();
		
		$attrs = $this->object->getAttributes();
		
		foreach( $attrs as $key => $attr )
		{
			$this->object->setAttributeVisible( $key, in_array($key, $visible) ); 
		}
		
	}
	
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