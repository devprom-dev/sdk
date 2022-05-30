<?php
include_once "ModelValidatorType.php";

class ModelValidatorTypeFile extends ModelValidatorType
{
	public function applicable( $type_name ) {
		return $type_name == 'file';
	}
	
	public function validate( & $value, array $groups = array() ) {
		return true;
	}
}