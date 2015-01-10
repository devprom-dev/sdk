<?php

include_once SERVER_ROOT_PATH.'pm/views/ui/BulkForm.php';

class RequestBulkForm extends BulkForm
{
 	function getForm()
 	{
 		return new RequestForm();
 	}
 	
	function getAttributeType( $attr )
 	{
 		switch ( $attr )
 		{
 			case 'SubmittedVersion':
 			case 'ClosedInVersion':
 			case 'Tag':
 			case 'Project':
 			case 'Iterations':
 			case 'Owner':
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
 				
 			case 'Project':
 				return translate('Проект');

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
 		global $model_factory;
 		
 		switch ( $attribute )
 		{
 			case 'SubmittedVersion':
 			case 'ClosedInVersion':
				$version = $model_factory->getObject('Version');

				$field = new FieldAutoCompleteObject( $version );
				$field->SetName($attribute);
				$field->SetId('Version');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				
				$field->draw();
				
				break;

 			case 'Project':

 				$field = new FieldAutoCompleteObject(getFactory()->getObject('ProjectAccessible'));
 				
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				$field->SetRequired(true);
				
				echo $this->getName($attribute);
				
				$field->draw();
				
				break;

 			case 'Iterations':
				
 			    $iteration = $model_factory->getObject('Iteration');
 			    
 			    $iteration->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
 			    
 			    $field = new FieldAutoCompleteObject( $iteration );
				
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				
				$field->draw();
				
				break;
				
 			case 'Tag':
				$field = new FieldAutoCompleteObject( $model_factory->getObject('Tag') );
				$field->SetId($attribute);
				$field->SetName('value');
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				
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
				
			case 'Owner':
    			$worker = $model_factory->getObject('Participant');
    			
    			$worker->addFilter( new ParticipantWorkerPredicate() );
				
				$field = new FieldDictionary( $worker );
				
				$field->SetId($attribute);
				$field->SetName($attribute);
				$field->SetValue($value);
				$field->SetTabIndex($tab_index);
				
				echo $this->getName($attribute);
				
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
	
	function getActionAttributes()
	{
		$attributes = parent::getActionAttributes();
		
		$key = array_search('Fact', $attributes);
		
		if ( $key !== false ) unset($attributes[$key]);
		
		return $attributes;
	}
		
	function getBulkActions( $object_it = null )
	{
	    if ( !is_object($object_it) ) $object_it = $this->getIt();
	    
		$actions = parent::getBulkActions($object_it);
		
		if ( !getFactory()->getAccessPolicy()->can_modify($object_it) ) return $actions;
		
		if ( count($actions) > 0 ) $actions[count($actions).'-'] = '';
		
		$key = 'Method:ModifyRequestWebMethod:Tag';
		$actions[$key] = text(861);

		$key = 'Method:ModifyRequestWebMethod:RemoveTag';
		$actions[$key] = text(862);

		if ( count($actions) > 0 ) $actions[count($actions).'-'] = '';

		$method = new DuplicateIssuesWebMethod();
		$key = $method->getMethodName();
		$actions[$key] = text(867);
		
		return $actions;
	}
}
 