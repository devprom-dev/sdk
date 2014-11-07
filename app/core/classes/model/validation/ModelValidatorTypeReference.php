<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeReference extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return strpos($type_name, "ref_") !== false;
	}
	
	public function validate( & $value )
	{
		$ids = array_filter(preg_split('/,/', $value), function( $value ) 
		{
			return strval(intval($value)) == $value;
		});

		return $value == '' || count($ids) > 0;
	}
}