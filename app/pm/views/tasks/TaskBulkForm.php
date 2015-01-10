<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class TaskBulkForm extends BulkForm
{
 	function getForm()
 	{
 		return new TaskForm();
 	}
 	
 	function IsAttributeModifiable( $attr )
	{
	    switch ( $attr ) 
	    {
	        case 'Project':
	            return true;
	            
	        default:
	            return parent::IsAttributeModifiable( $attr );
	    }
	}
 	
 	function getBulkActions( $object_it )
 	{
 		return parent::getBulkActions($object_it);
 	}
 	
 	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Release':
 			case 'Project':
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
				
 			case 'Project':
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessible'));
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->SetRequired(true);
				
				echo $field->getName();
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
