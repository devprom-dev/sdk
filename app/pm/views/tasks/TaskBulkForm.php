<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class TaskBulkForm extends BulkForm
{
 	function getForm()
 	{
 		return new TaskForm($this->getObject());
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
 	
 	function getAttributeType( $attr )
 	{
 		return 'custom';
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
 		switch ( $attribute )
 		{
 			case 'Release':
				$field = new FieldAutoCompleteObject(getFactory()->getObject('Iteration'));
				$field->SetName($attribute);
				$field->SetId('Release');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);

 		 		if ( $this->showAttributeCaption() ) {
					echo $this->getObject()->getAttributeUserName($attribute);
				}
				$field->draw();
				break;
				
 			case 'Project':
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessible'));
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->SetRequired(true);
				
 				if ( $this->showAttributeCaption() ) {
					echo $this->getObject()->getAttributeUserName($attribute);
				}
				$field->draw();
				break;
				
			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index );
 		}
 	}
}
