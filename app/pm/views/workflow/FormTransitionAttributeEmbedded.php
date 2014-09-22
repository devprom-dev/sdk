<?php

include_once "FieldAttributeDictionary.php";

class FormTransitionAttributeEmbedded extends PMFormEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'ReferenceName':
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
		global $model_factory;
		
		switch( $attr_name )
		{
			case 'ReferenceName':
				$class = $this->object->getDefaultAttributeValue( 'Entity' );
				
				return new FieldAttributeDictionary( $model_factory->getObject( $class ) );

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}