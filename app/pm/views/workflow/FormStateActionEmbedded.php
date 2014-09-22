<?php

class FormStateActionEmbedded extends PMFormEmbedded
{
 	var $entity;
 	
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
 	
 	function setEntity( $entity )
 	{
 		$this->entity = $entity;
 	}
 	
	function createField( $attr_name ) 
	{
		global $model_factory, $_REQUEST;
		
		switch( $attr_name )
		{
			case 'ReferenceName':
				
				$object = $model_factory->getObject('StateBusinessAction');
				
				$object->addFilter( new StateBusinessEntityFilter($this->entity) );
				
				return new FieldDictionary( $object	);

			default:
				return parent::createField( $attr_name );
		}
	}
}