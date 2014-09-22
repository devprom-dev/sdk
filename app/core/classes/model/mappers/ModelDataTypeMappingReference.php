<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingReference extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return strpos($type_name, 'ref_') !== false;
	}
	
	public function map( $value )
	{
		if ( !is_numeric($value) ) return '';
	
		return $value;
	}
}