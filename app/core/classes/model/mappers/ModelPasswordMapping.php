<?php

class ModelPasswordMapping
{
    public function map( Metaobject $object, &$values )
    {
        $attributes = $object->getAttributesByType('password');
        foreach( $attributes as $attribute ) {
            if ( $values[$attribute] == SHADOW_PASS ) {
                unset($values[$attribute]);
                if ( $values['Repeat' . $attribute] == SHADOW_PASS ) {
                    $values['Repeat' . $attribute] = '';
                }
            }
        }
    }
}