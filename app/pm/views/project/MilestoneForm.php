<?php

include_once SERVER_ROOT_PATH.'pm/views/issues/FieldIssueInverseTrace.php';

class MilestoneForm extends PMPageForm
{
 	function IsAttributeVisible( $attr_name ) 
 	{
 		switch($attr_name) 
 		{
 			case 'OrderNum':
 				return false;
 		}
		return parent::IsAttributeVisible( $attr_name );
	}

	function getActions()
	{
		$actions = parent::getActions();
		
		$object_it = $this->getObjectIt();
		
		if ( !is_object($object_it) ) return;
		
		$method = $object_it->get('Passed') == 'Y'
			? new SetCurrentWebMethod : new SetPassedWebMethod;  
		
		if( $method->hasAccess() )
		{
			if ( !$this->IsFormDisplayed() ) $method->setRedirectUrl('donothing');
			
		    if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
		    
			array_push($actions, array( 
				'name' => $method->getCaption(),
				'url' => $method->getJSCall(array('Milestone' => $object_it->getId())) ) );
		}
		
		return $actions;
	}
	
	function createFieldObject( $name )
	{
		switch ( $name )
		{
			case 'TraceRequests':
				
				return new FieldIssueInverseTrace( $this->getObjectIt(), getFactory()->getObject('RequestInversedTraceMilestone') );

			default:
				
				return parent::createFieldObject( $name );
		}
	}
}
