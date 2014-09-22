<?php

include SERVER_ROOT_PATH."pm/views/ui/BulkForm.php";
include_once SERVER_ROOT_PATH."pm/views/wiki/fields/FieldWikiPage.php";

class WikiBulkForm extends BulkForm
{
	function getWidth()
	{
		return '70%';
	}
	
 	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 		    case 'ParentPage':
 		    case 'Project':
 		    	return 'custom';

 			case 'CopyOption':
 				return 'char';
 		    	
 			default:
 				return parent::getAttributeType( $attr );
 		}
 	}

 	function getName( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'CopyOption':
 				return text(1726);
 			
 			case 'Project':
 				return translate('Проект');
 				
 			default:
 				return parent::getName( $attr );
 		}
 	}
 	
 	function getDescription( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'CopyOption':
 				return text(1727);
 				
 			default:
 				return parent::getDescription( $attr );
 		}
 	}
 	
	function getAttributeValue( $attribute )
	{
		switch ( $attribute )
		{
		    case 'CopyOption': 
		    	return 'Y';
		    	
		    default:
		    	return parent::getAttributeValue( $attribute );
		}
	}
 	
 	function drawCustomAttribute( $attribute, $value, $tab_index )
 	{
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 			case 'Project':

 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessible'));
				
 				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				
				$field->draw();
 								
				break;
				
 			case 'ParentPage':

			    $object = $model_factory->getObject(get_class($this->getObject()));

		        $object->addFilter( new FilterBaseVpdPredicate() );
			    
				$field = new FieldWikiPage( $object );
				
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				$field->draw();
				
				$field->drawScripts();
				
				break;

			default:
 				parent::drawCustomAttribute( $attribute, $value, $tab_index );
 		}
 	}

 	function IsAttributeModifiable( $attr )
	{
		switch ( $attr )
		{
			case 'PageType':
			case 'Project':
			case 'ParentPage':
				return true;
				
			case 'ReferenceName':
			case 'Content':
			case 'IsTemplate':
			case 'IsDraft':
			case 'IsArchived':
			case 'ContentEditor':
			case 'UserField1':
			case 'UserField2':
			case 'UserField3':
			    return false;
				
			default:
				return parent::IsAttributeModifiable( $attr );
		}
	}
}