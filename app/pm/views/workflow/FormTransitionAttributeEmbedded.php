<?php

include_once "FieldAttributeDictionary.php";

class FormTransitionAttributeEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ReferenceName':
            case 'IsVisible':
            case 'IsRequired':
 				return true;
 			default:
 				return false;
 		}
 	}
 	
 	function IsAttributeObject( $attr_name )
 	{
 		switch ( $attr_name )
 		{
 			case 'ReferenceName':
 				return true;
 			default:
 				return parent::IsAttributeObject( $attr_name );
 		}
 	}
 	
 	function createField( $attr_name ) 
	{
		switch( $attr_name )
		{
			case 'ReferenceName':
				return new FieldAttributeDictionary( getFactory()->getObject(
                    $this->object->getDefaultAttributeValue( 'Entity' )
                ));
			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}