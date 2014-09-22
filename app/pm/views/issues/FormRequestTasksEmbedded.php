<?php

include_once "FormTaskEmbedded.php";

class FormRequestTasksEmbedded extends FormTaskEmbedded
{
 	function IsAttributeVisible( $attribute )
 	{
 		switch ( $attribute )
 		{
 			case 'Release':
 				return true;

 			default:
 				return parent::IsAttributeVisible( $attribute ); 
 		}
 	}
 	
 	function getItemDisplayName( $object_it )
 	{
 		$uid = new ObjectUID;
 		
 		return $uid->getUidWithCaption( $object_it ).' ('.$object_it->getStateName().')';
 	}
 	
 	function createField( $attr )
 	{
 	    global $model_factory;
 	    
 	    switch ( $attr )
 	    {
 	        case 'Release':
 	            
 	            $object = $model_factory->getObject('Iteration');
 	            
 	            $object->addFilter( new IterationTimelinePredicate(IterationTimelinePredicate::NOTPASSED) );
 	            
				return new FieldDictionary( $object );
 	             	
 	        default:
 	            return parent::createField( $attr );
 	    }
 	} 
 	
 	function getActions( $object_it, $item )
 	{
 		return array_merge( $this->getTaskActions($object_it), parent::getActions( $object_it, $item ));
 	}
 	
	function getTaskActions( $object_it )
	{
		$actions = array();
		
		$todo = ($_REQUEST['formonly'] != '' ? 'donothing' : 'function() { window.location.reload(); }');
		$url = ($_REQUEST['formonly'] != '' ? 'click' : 'url');
		
		$method = new ObjectModifyWebMethod($object_it);
		
		$method->setRedirectUrl($todo);
		
		$actions[] = array (
				'name' => translate('Изменить'),
				'url' => $method->getJSCall()
		);
		
		$state_it = $object_it->getStateIt();
		
		$transition_it = $state_it->getTransitionIt();

		$need_separator = true;
		
		while ( !$transition_it->end() )
		{
			$method = new TransitionStateMethod( $transition_it, $object_it );
			
			if ( $method->hasAccess() )
			{
				if ( $need_separator )
				{
					$actions[] = array();
					
					$need_separator = false;
				}

				$method->setRedirectUrl($todo);
				
				$actions[] = array( 
					$url => $method->getJSCall(), 
					'name' => $method->getCaption()
				);
			}
			
			$transition_it->moveNext();
		}
		
		$actions[] = array();
		
		return $actions;
	}
}
