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
	
	protected function getContentTypeByFileName( $file_name )
	{
		$parts = pathinfo($file_name);
 				
 		switch ( $parts['extension'] )
 		{
			case 'php':
			case 'php3':
			case 'php4':
			case 'php5':
			case 'phtml':
			case 'html':
			case 'htm':
			case 'css':
			case 'cpp':
 			case 'c':
 			case 'h':
 			case 'cs':
 			case 'config':
 			case 'java':
 			case 'txt':
 			case 'xml':
 			case 'js':
 			case 'json':
 			case 'coffee':
 				return 'text/html; charset="utf-8"';
 		}

 		return '';
	}
}
