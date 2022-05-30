<?php
include_once "ModelValidatorType.php";

class ModelValidatorTypeEmail extends ModelValidatorType
{
	public function applicable( $type_name ) {
		return in_array($type_name, array('email'));
	}

	public function validate( & $value, array $groups = array() ) {
		return filter_var($value, FILTER_VALIDATE_EMAIL);
	}
}