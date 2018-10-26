<?php

include_once "ModelValidatorInstance.php";

class ModelValidatorObligatory extends ModelValidatorInstance
{
	private $attributes = array();
	
	public function __construct( $attributes = array() )
	{
		$this->attributes = $attributes;
	}
	
	public function validate( Metaobject $object, array & $parms )
	{
        $attributes = $this->attributes;
	    if ( count($attributes) < 1 ) {
            foreach( array_keys($object->getAttributes()) as $attribute ) {
                if (!$object->IsAttributeStored($attribute)) continue;
                $attributes[] = $attribute;
            }
        }

		foreach( $attributes as $attribute )
		{
			if ( !array_key_exists($attribute, $parms) ) continue;
			if ( !$object->IsAttributeRequired($attribute) ) continue;
			
			switch ( $object->getAttributeType($attribute) )
			{
			    case 'file':
			    	break;
			    	
			    default:
					if ( $parms[$attribute] == '' && $object->getDefaultAttributeValue($attribute) == "" )
					{
						return text(2).': '.translate($object->getAttributeUserName($attribute))." [".$attribute."]";
					}
			}
		}
		
		return "";
	}
}