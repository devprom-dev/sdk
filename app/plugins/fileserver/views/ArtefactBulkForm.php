<?php

if ( !class_exists('BulkForm', false) ) include SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class ArtefactBulkForm extends BulkForm
{
 	function getForm()
 	{
 		return new ArtefactForm();
 	}
 	
 	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Version':
 				return 'custom';
 				
 			default:
 				return parent::getAttributeType( $attr );
 		}
 	}
 	
 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function drawCustomAttribute( $attribute, $value, $tab_index )
 	{
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 			case 'Version':
				$field = new FieldAutoCompleteObject( $model_factory->getObject('Version') );
				$field->SetName($attribute);
				$field->SetId('Version');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->draw();
 				break;
				
			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index );
 		}
 	}
 	
	function getWidth()
	{
		return '65%';
	}
}