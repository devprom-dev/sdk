<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class RequestBulkForm extends BulkForm
{
 	function buildForm()
 	{
 		return new RequestForm($this->getObject());
 	}
 	
	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Tag':
 			case 'Project':
 			case 'Iterations':
 			case 'LinkType':
 				return 'custom';
 				
 			default:
 				return parent::getAttributeType( $attr );
 		}
 	}
 	
 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'Tag':
 				return translate('Тэг');
 				
 			case 'Iterations':
 				return translate('Итерация');

 			case 'LinkType':
 				return translate('Тип связи');
 				
 			default:
 				return parent::getName( $attr );
 		}
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
	        case 'Iterations':
	            return true;
	            
	        default:
	            return parent::IsAttributeModifiable( $attr );
	    }
	}
 	
 	function drawCustomAttribute( $attribute, $value, $tab_index )
 	{
 		switch ( $attribute )
 		{
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

 			case 'Iterations':
 			    $iteration = getFactory()->getObject('Iteration');
 			    $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
 			    $field = new FieldAutoCompleteObject( $iteration );
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
 				if ( $this->showAttributeCaption() ) {
					echo $this->getName($attribute);
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
				
 			case 'LinkType':
 				$type = getFactory()->getObject('RequestLinkType');
 				
				$field = new FieldAutoCompleteObject($type);
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($type->getByRef('ReferenceName', 'implemented')->getId());
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				$field->draw();
				
				break;

			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index );
 		}
 	}
}
 