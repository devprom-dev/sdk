<?php
include_once "ModelDataTypeMapper.php";

class ModelMapper
{
	private $instances = array();
	
	public function __construct( $validators = array() ) {
		$this->instances = $validators;
	}
	
	public function map( Metaobject $object, array & $parms )
	{
		foreach( $this->instances as $mapper ) {
			$mapper->map($object, $parms);
		}
	}
}