<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypePassword extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('password'));
	}

	public function validate( & $value )
	{
		return true;
	}
}