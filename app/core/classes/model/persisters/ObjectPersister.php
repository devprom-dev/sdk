<?php

class ObjectPersister
{
 	var $object;
 	
 	function __construct()
 	{
 	}
 	
 	function getId()
 	{
 		return get_class($this);
 	}
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function add( $object_id, $parms )
 	{
 	}
 	
 	function modify( $object_id, $parms )
 	{
 	}
 	
 	function delete( $object_id )
 	{
 	}
}