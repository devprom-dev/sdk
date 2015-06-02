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
		if ( $value == '' ) return '';

		$value = str_replace(',', '.', $value);
		if ( !is_numeric($value) ) return '0';
	
		return $value;
	}
}