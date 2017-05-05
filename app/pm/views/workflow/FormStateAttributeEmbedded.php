<?php

include_once "FieldAttributeDictionary.php";

class FormStateAttributeEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ReferenceName':
 			case 'IsVisible':
 			case 'IsRequired':
            case 'IsReadonly':
            case 'IsMainTab':
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
				return new FieldAttributeDictionary(
						getFactory()->getObject( 
								$this->getObject()->getAttributeObject('State')->getObjectClass() 
						)
				);

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}