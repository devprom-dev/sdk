<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingFloat extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('float'));
	}
	
	public function map( $value )
	{
		$value = str_replace(',', '.', $value);
		
		if ( !is_numeric($value) ) return 0.0;
	
		return $value;
	}
}