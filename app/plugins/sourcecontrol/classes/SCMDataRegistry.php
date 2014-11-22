<?php

class SCMDataRegistry extends ObjectRegistry
{
	private $data = array();
	
	public function __construct()
	{
		global $model_factory;
		
		parent::__construct( $model_factory->getObject('entity') );
	}

	public function addData( $parms )
	{
		$this->data[] = $parms;
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function setData( $data )
	{
		$this->data = $data;
	}
	
	public function getAll()
	{
		return $this->createIterator( $this->data );
	}

	public function getFirst()
	{
	}

	public function Store( OrderedIterator $object_it, array $data )
	{
	}
}
