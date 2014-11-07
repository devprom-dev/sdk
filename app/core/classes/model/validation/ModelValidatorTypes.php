<?php

include_once "ModelValidatorInstance.php";
include_once "ModelValidatorTypeNumeric.php";
include_once "ModelValidatorTypeBoolean.php";
include_once "ModelValidatorTypeDate.php";
include_once "ModelValidatorTypeNull.php";
include_once "ModelValidatorTypeFile.php";
include_once "ModelValidatorTypeReference.php";
include_once "ModelValidatorTypePassword.php";

class ModelValidatorTypes extends ModelValidatorInstance
{
	private $attributes = array();
	
	public function __construct( $attributes = array() )
	{
		$this->attributes = $attributes;
		
		$this->type_validators = array (
				new ModelValidatorTypeNumeric(),
				new ModelValidatorTypeBoolean(),
				new ModelValidatorTypeDate(),
				new ModelValidatorTypeReference(),
				new ModelValidatorTypePassword(),
				new ModelValidatorTypeFile()
		);
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
		$attributes = count($this->attributes) < 1 
				? array_keys($object->getAttributesSorted()) : $this->attributes;
		
		foreach( $attributes as $attribute )
		{
			if ( !array_key_exists($attribute, $parms) ) continue;
			if ( !$object->IsAttributeStored($attribute) ) continue;
				
			$validator = $this->getTypeValidator( $object->getAttributeType($attribute) );
			
			if ( !$validator->validate($parms[$attribute]) )
			{
				$result = translate('Вы указали некорректное значение поля').' "'.
						translate($object->getAttributeUserName($attribute)).'" ['.$attribute.']';
				
				if ( $object->getAttributeTypeName($attribute) != '' )
				{
					$result .= ' ('.$object->getAttributeTypeName($attribute).")"; 
				}
				
				$result .= ': '.$parms[$attribute];
	
				return $result;
			}
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

	private $type_validators = array();
}