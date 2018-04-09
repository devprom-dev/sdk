<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingString extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('varchar','text','largetext'));
	}
	
	public function map( $value )
	{
		return trim(html_entity_decode($value), " \r\n");
	}
}
