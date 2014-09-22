<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeBoolean extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('char'));
	}
	
	public function validate( & $value )
	{
		return in_array($value, array('on', 'N', 'Y'));
	}
}