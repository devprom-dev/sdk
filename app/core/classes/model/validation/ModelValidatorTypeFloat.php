<?php
include_once "ModelValidatorType.php";

class ModelValidatorTypeFloat extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('float'));
	}
	
	public function validate( & $value )
	{
        if ( in_array($value, array('','NULL')) ) return true;

		$match = array();
		if ( preg_match(SystemDateTime::getTimeParseRegex(), $value, $match) and count($match) > 1 ) return true;

		$value = str_replace(',', '.', $value);
		if( !is_numeric($value) ) return false;

		return true;
	}
}