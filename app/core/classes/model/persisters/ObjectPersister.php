<?php

class ObjectPersister
{
 	private $object = null;
 	
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

	public function __sleep()
	{
		unset($this->object);
		$this->object = null;
	}
	
	public function __destruct()
	{
		unset($this->object);
		$this->object = null;
	}
	
	public function __wakeup()
	{
		$this->object = null;
	}
}