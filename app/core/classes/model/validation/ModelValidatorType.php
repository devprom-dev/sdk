<?php

abstract class ModelValidatorType
{
	abstract public function applicable( $type_name );
	abstract public function validate( & $value );

	public function getMessage() {
		return '';
	}
}