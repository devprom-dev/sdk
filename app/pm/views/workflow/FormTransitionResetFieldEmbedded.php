<?php

class FormTransitionResetFieldEmbedded extends PMFormEmbedded
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
				
				$field = new FieldAttributeDictionary( $model_factory->getObject( $class ) );
                
                return $field;

			default:
				return parent::createFieldObject( $attr_name );
		}
	}
}