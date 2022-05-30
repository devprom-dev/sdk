<?php

include_once "ModelValidatorInstance.php";
include_once "ModelValidatorTypeNumeric.php";
include_once "ModelValidatorTypeFloat.php";
include_once "ModelValidatorTypeBoolean.php";
include_once "ModelValidatorTypeDate.php";
include_once "ModelValidatorTypeNull.php";
include_once "ModelValidatorTypeFile.php";
include_once "ModelValidatorTypeEmail.php";
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
            new ModelValidatorTypeFloat(),
            new ModelValidatorTypeBoolean(),
            new ModelValidatorTypeDate(),
            new ModelValidatorTypeReference(),
            new ModelValidatorTypePassword(),
            new ModelValidatorTypeFile(),
            new ModelValidatorTypeEmail()
		);
	}
	
	public function validate( Metaobject $object, array $parms )
	{
		$attributes = count($this->attributes) < 1 
				? array_keys($object->getAttributes()) : $this->attributes;
		
		foreach( $attributes as $attribute )
		{
			if ( !array_key_exists($attribute, $parms) ) continue;
			if ( !$object->IsAttributeStored($attribute) ) continue;

			$validator = $this->getTypeValidator( $object->getAttributeType($attribute) );
			if ( !$validator->validate($parms[$attribute], $object->getAttributeGroups($attribute)) )
			{
				$result = $validator->getMessage();
				if ( $result == '' ) {
					$result = translate('Вы указали некорректное значение поля').' "'.
						translate($object->getAttributeUserName($attribute)).'" ['.$attribute.']';

                    $typeName = $object->getAttributeTypeName($attribute);
					if ( $typeName != '' ) {
                        if ( in_array('hours', $object->getAttributeGroups($attribute)) ) {
                            $typeName = text(3306);
                        }
                        if ( in_array('daily-hours', $object->getAttributeGroups($attribute)) ) {
                            $typeName = sprintf(text(3305), defined('MAX_DAILY_HOURS') ? MAX_DAILY_HOURS : 24);
                        }
						$result .= " ({$typeName})";
					}
					$result .= ': '.$parms[$attribute];
				}
				return $result;
			}
		}
		
		return "";
	}
	
	private function getTypeValidator( $type )
	{
		foreach( $this->type_validators as $validator )	{
			if ( $validator->applicable( strtolower($type) ) ) return $validator; 
		}
		return new ModelValidatorTypeNull();
	}

	private $type_validators = array();
}