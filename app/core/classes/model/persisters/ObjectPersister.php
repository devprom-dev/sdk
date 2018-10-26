<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class ObjectPersister
{
    protected $attributes = array();
 	private $object = null;

 	function __construct( array $attributes = array() )
 	{
		$this->attributes = $attributes;
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

	function getAttributes()
	{
		return $this->attributes;
	}

	function IsPersisterImportant() {
        return false;
    }

	function map( & $parms )
	{
	}

 	function add( $object_id, $parms )
 	{
 	}
 	
 	function modify( $object_id, $parms )
 	{
 	}

	function beforeDelete( $object_id )
	{
	}

 	function afterDelete( $object_it )
 	{
 	}

	public function __sleep()
	{
		unset($this->object);
		$this->object = null;
		return array('attributes');
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