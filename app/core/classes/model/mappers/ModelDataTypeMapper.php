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
include_once "ModelDataTypeMappingPassword.php";
include_once "ModelDataTypeMappingFile.php";

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
				new ModelDataTypeMappingString(),
				new ModelDataTypeMappingPassword(),
				new ModelDataTypeMappingFile()
		);
	}
	
	public function map( Metaobject $object, & $values )
	{
		$skip_attributes = $object->getAttributesByGroup('skip-mapper');

		foreach( $object->getAttributes() as $attribute => $attribute_data )
		{
			if ( !array_key_exists($attribute, $values) ) continue;
			if ( in_array($attribute, $skip_attributes) ) continue;

			$mapped_value = $this->getMapper($object->getAttributeType($attribute))->mapInstance($attribute, $values);
			if ( is_null($mapped_value) ) {
				unset($values[$attribute]);
			}
			else {
				$values[$attribute] = $mapped_value;
			}
		}
	}
	
	public function getMapper( $type )
	{
		foreach( $this->mappers as $mapper ) {
			if ( $mapper->applicable(strtolower($type)) ) return $mapper;
		}
		return new ModelDataTypeMappingNull();
	}
}