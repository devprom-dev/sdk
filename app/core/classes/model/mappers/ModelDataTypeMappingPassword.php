<?php

include_once "ModelDataTypeMapping.php";

class ModelDataTypeMappingPassword extends ModelDataTypeMapping
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('password'));
	}
	
	public function map( $value )
	{
		if ( $value == SHADOW_PASS ) return '';
	
		return $value;
	}
}