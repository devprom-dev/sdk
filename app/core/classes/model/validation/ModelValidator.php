<?php

include_once "ModelValidatorTypes.php";
include_once "ModelValidatorObligatory.php";
include_once "ModelValidatorUnique.php";

class ModelValidator
{
	private $instance_validators = array();
	
	public function __construct( $validators = array() )
	{
		$this->instance_validators = $validators;
	}
	
	public function addValidator( $validator )
	{
		$this->instance_validators[] = $validator;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		foreach( $this->instance_validators as $validator )
		{
			$result = $validator->validate($object, $parms);

			if ( $result != "" ) return $result;
		}
		
		return "";
	}
}