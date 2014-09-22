<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class TaskBulkForm extends BulkForm
{
 	function getForm()
 	{
 		return new TaskForm();
 	}
 	
 	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Release':
 				return 'custom';
 				
 			default:
 				return parent::getAttributeType( $attr );
 		}
 	}
 	
 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Release':
 				return translate('Итерация');
 				
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function drawCustomAttribute( $attribute, $value, $tab_index )
 	{
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 			case 'Release':
				$field = new FieldAutoCompleteObject( $model_factory->getObject('Iteration') );
				$field->SetName($attribute);
				$field->SetId('Release');
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
