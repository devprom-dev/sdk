<?php

include_once "ModelValidatorType.php";

class ModelValidatorTypeNumeric extends ModelValidatorType
{
	public function applicable( $type_name )
	{
		return in_array($type_name, array('integer', 'float', 'price'));
	}
	
	public function validate( & $value )
	{
		if ( $value == '' ) return true;
		
		$values = preg_split('/,/', $value);
		
		foreach( $values as $value )
		{
			$value = str_replace(',', '.', $value);
			
			if( !is_numeric($value) ) return false; 
		}
		
		return true;
	}
}