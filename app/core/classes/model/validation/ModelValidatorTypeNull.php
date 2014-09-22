<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeNull extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return true;
	}
	
	public function validate( & $value )
	{
		return true;
	}
}