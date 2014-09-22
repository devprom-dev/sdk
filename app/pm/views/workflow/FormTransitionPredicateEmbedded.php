<?php

class FormTransitionPredicateEmbedded extends PMFormEmbedded
{
 	var $entity;
 	
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Predicate':
 				return true;

 			default:
 				return false;
 		}
 	}
 	
 	function getNoItemsMessage()
 	{
 		return text(1141);
 	}
 	
 	function setEntity( $entity )
 	{
 		$this->entity = $entity;
 	}
 	
	function createField( $attr_name ) 
	{
		global $model_factory;
		
		switch( $attr_name )
		{
			case 'Predicate':
				
				$object = $model_factory->getObject('StateBusinessRule');
				
				$object->addFilter( new StateBusinessEntityFilter($this->entity) );
				
				return new FieldDictionary( $object	);

			default:
				return parent::createField( $attr_name );
		}
	}
}