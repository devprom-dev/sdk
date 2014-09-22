<?php

include_once "ModelValidatorTypeNumeric.php";
include_once "ModelValidatorTypeBoolean.php";
include_once "ModelValidatorTypeDate.php";
include_once "ModelValidatorTypeNull.php";
include_once "ModelValidatorTypeReference.php";
include_once "ModelValidatorTypePassword.php";

class ModelValidator
{
	private $type_validators = array();
	
	private $instance_validators = array();
	
	public function __construct()
	{
		$this->type_validators = array (
				new ModelValidatorTypeNumeric(),
				new ModelValidatorTypeBoolean(),
				new ModelValidatorTypeDate(),
				new ModelValidatorTypeReference(),
				new ModelValidatorTypePassword()
		);
	}
	
	public function addValidator( $validator )
	{
		if ( $validator instanceof ModelValidatorType )
		{
			$this->type_validators[] = $validator;
		}
		
		if ( $validator instanceof ModelValidatorInstance )
		{
			$this->instance_validators[] = $validator;
		}
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$result = $this->validateType( $object, $parms );
		
		if ( $result != "" )
		{
			$result = translate('Вы указали некорректное значение поля').' "'.translate($object->getAttributeUserName($result)).'" ';
			
			if ( $object->getAttributeTypeName($result) != '' )
			{
				$result .= ' ('.$object->getAttributeTypeName($result).")"; 
			}

			return $result;
		}
		
		$result = $this->validateInstance( $object, $parms );

		if ( $result != "" ) return $result;
		
		return $result;
	}
	
	public function validateInstance( Metaobject $object, array & $parms )
	{
		foreach( $this->instance_validators as $validator )
		{
			$result = $validator->validate($object, $parms);

			if ( $result != "" ) return $result;
		}
		
		return "";
	}
	
	public function validateType( Metaobject $object, array & $parms )
	{
		$attributes = $object->getAttributesSorted();
		
		foreach( $attributes as $attribute => $attrbute_data )
		{
			if ( !array_key_exists($attribute, $parms) || $parms[$attribute] == '') continue;
			
			if ( !$object->IsAttributeStored($attribute) ) continue;
				
			$validator = $this->getTypeValidator( $object->getAttributeType($attribute) );
			
			if ( !$validator->validate($parms[$attribute]) ) return $attribute;
		}

		return "";
	}
	
	private function getTypeValidator( $type )
	{
		foreach( $this->type_validators as $validator )
		{
			if ( $validator->applicable( strtolower($type) ) ) return $validator; 
		}
		
		return new ModelValidatorTypeNull();
	}
}