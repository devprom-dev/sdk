<?php
include_once "ModelValidatorInstance.php";

class ModelValidatorObligatory extends ModelValidatorInstance
{
	private $attributes = array();
	
	public function __construct( $attributes = array() ) {
		$this->attributes = $attributes;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
        $attributes = $this->attributes;
	    if ( count($attributes) < 1 ) {
            foreach( array_keys($object->getAttributes()) as $attribute ) {
                if (!$object->IsAttributePersisted($attribute)) continue;
                $attributes[] = $attribute;
            }
        }

		foreach( $attributes as $attribute )
		{
			if ( !$object->IsAttributeRequired($attribute) ) continue;
			if ( $object->getAttributeType($attribute) == 'file' ) continue;
            if ( !array_key_exists($attribute, $parms) ) continue;

            $groups = $object->getAttributeGroups($attribute);
            $valueDefined = in_array('multiselect', $groups)
                ? count($parms[$attribute]) > 0
                : trim($parms[$attribute]) != '';

            if ( !$valueDefined ) {
                $defaultValue = $object->getDefaultAttributeValue($attribute);
                if ( $defaultValue != '' ) {
                    $parms[$attribute] = $defaultValue;
                }
                else {
                    return text(2).': '.translate($object->getAttributeUserName($attribute))." [".$attribute."]";
                }
            }
		}
		
		return "";
	}
}