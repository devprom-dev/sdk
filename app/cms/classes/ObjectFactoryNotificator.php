<?php

abstract class ObjectFactoryNotificator
{
 	var $recordData;
	
	function __construct() 
	{
	}
	
	function setRecordData( $data )
	{
	    $this->recordData = $data;
	}
	
	function getRecordData()
	{
	    return $this->recordData;
	}
	
 	abstract function add( $object_it );

 	abstract function modify( $prev_object_it, $object_it );

 	abstract function delete( $object_it );
}
