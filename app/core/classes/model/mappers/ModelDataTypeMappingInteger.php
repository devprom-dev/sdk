<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingInteger extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('integer'));
	}
	
	public function map( $value )
	{
		$value = str_replace(',', '.', $value);
		
		if ( $value != '' && !is_numeric($value) ) return 0;
	
		return $value;
	}
}