<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeNumeric extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('integer', 'price'));
	}
	
	public function validate( & $value )
	{
		if ( in_array($value, array('','NULL')) ) return true;
		
		$values = preg_split('/,/', $value);
		foreach( $values as $value ) {
			if( !is_numeric($value) ) return false;
		}
		
		return true;
	}
}