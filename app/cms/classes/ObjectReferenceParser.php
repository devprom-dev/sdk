<?php

class ObjectReferenceParser
{
 	private $object = null;
 	
 	function setObject( $object )
 	{
 		$this->object = $object;
 	}
 	
 	function getObject()
 	{
 		return $this->object;
 	}
 	
 	function parse( $reference_name, $attribute_type )
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