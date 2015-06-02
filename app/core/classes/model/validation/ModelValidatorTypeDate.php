<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeDate extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('date', 'datetime'));
	}
	
	public function validate( & $value )
	{
		return $value == '' || strtotime($value) < 1 || getLanguage()->getDbDate($value) != "";
	}
}