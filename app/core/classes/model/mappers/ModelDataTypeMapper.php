<?php

include_once "ModelDataTypeMappingDate.php";
include_once "ModelDataTypeMappingDateTime.php";
include_once "ModelDataTypeMappingNull.php";
include_once "ModelDataTypeMappingBoolean.php";
include_once "ModelDataTypeMappingInteger.php";
include_once "ModelDataTypeMappingFloat.php";
include_once "ModelDataTypeMappingReference.php";
include_once "ModelDataTypeMappingTag.php";
include_once "ModelDataTypeMappingString.php";

class ModelDataTypeMapper
{
	private $mappers = array();
	
	public function __construct()
	{
		$this->mappers = array (
				new ModelDataTypeMappingTag(),
				new ModelDataTypeMappingDate(),
				new ModelDataTypeMappingDateTime(),
				new ModelDataTypeMappingBoolean(),
				new ModelDataTypeMappingInteger(),
				new ModelDataTypeMappingFloat(),
				new ModelDataTypeMappingReference(),
				new ModelDataTypeMappingString()
		);
	}
	
	public function map( Metaobject $object, & $values )
	{
		foreach( $object->getAttributes() as $attribute => $attribute_data )
		{
			$mapper = $this->getMapper($object->getAttributeType($attribute));
			
			if ( !array_key_exists($attribute, $values) ) continue;

			$values[$attribute] = $mapper->map($values[$attribute]); 
		}
	}
	
	public function getMapper( $type )
	{
		foreach( $this->mappers as $mapper )
		{
			if ( $mapper->applicable(strtolower($type)) ) return $mapper;
		}
		
		return new ModelDataTypeMappingNull(); 
	}
}