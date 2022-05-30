<?php

class ModelDefaultValuesMapping
{
	public function map( $object, &$values )
    {
        foreach( array_keys($object->getAttributes()) as $attribute )
        {
            if ( !$object->IsAttributePersisted($attribute) ) continue;
            if ( !$object->IsAttributeRequired($attribute) ) continue;
            if ( $object->getAttributeType($attribute) == 'file' ) continue;
            if ( !array_key_exists($attribute, $values) ) continue;

            $groups = $object->getAttributeGroups($attribute);
            $valueDefined = in_array('multiselect', $groups)
                ? count($values[$attribute]) > 0
                : trim($values[$attribute]) != '';

            if ( !$valueDefined ) {
                $values[$attribute] = $object->getDefaultAttributeValue($attribute);
            }
        }
	}
}
