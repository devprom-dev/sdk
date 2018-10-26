<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class TaskBulkForm extends BulkForm
{
 	function buildForm()
 	{
 		return new TaskForm($this->getObject());
 	}

    function IsAttributeVisible( $attribute )
    {
        switch( $attribute )
        {
            case 'RemoveTag':
                return false;
            default:
                return parent::IsAttributeVisible( $attribute );
        }
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
 		switch ( $attr )
 		{
 			case 'Release':
 			case 'Project':
            case 'Tag':
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
            case 'Tag':
                return translate('Тэг');
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function drawCustomAttribute( $attribute, $value, $tab_index, $view )
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
 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessibleActive'));
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

            case 'Tag':
                $field = new FieldAutoCompleteObject( getFactory()->getObject('Tag') );
                $field->SetId($attribute);
                $field->SetName('value');
                $field->SetValue($value);
                $field->SetTabIndex($tab_index);
                $field->setAppendable();

                if ( $this->showAttributeCaption() ) {
                    echo $this->getName($attribute);
                }
                $field->draw();
                break;

			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index, $view );
 		}
 	}
}
