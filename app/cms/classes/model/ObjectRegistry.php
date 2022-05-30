<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

abstract class ObjectRegistry
{
	protected $object = null;
	
	public function __construct( $object = null ) {
        $this->setObject($object);
	}
	
	public function __sleep()
	{
		unset($this->object);
		$this->object = null;
		return array();
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
		
	public function & getObject()
	{
		return $this->object;
	}
	
	public function setObject( & $object )
	{
		$this->object = $object;
	}
	
	public function createIterator( $rowset ) 
	{
		$iterator = $this->object->createIterator();

		$iterator->setRowset($rowset);
		
		$iterator->moveFirst();
		
		return $iterator;
	}
	
	abstract public function Store( OrderedIterator $object_it, array $data );

    public function Create( array $data ) {}

    public function Delete( OrderedIterator $object_it ) {}

    public function Merge( array $data, array $alternativeKey = array() ) {}
}