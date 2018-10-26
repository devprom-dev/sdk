<?php

use Devprom\ProjectBundle\Service\Workflow\WorkflowService;
include_once "BusinessActionShift.php";

class WikiPageBusinessActionReturnToWork extends BusinessActionShift
{
 	function getId()
 	{
 		return '4c3d7b22-ff66-4374-9871-60dd7b782490';
 	}
	
	function applyContent( $object_it, $attributes, $action = '' )
 	{
        if ( !in_array('Content', $attributes) ) return true;

		$state = array_shift($object_it->object->getNonTerminalStates());
		if ( $object_it->get('State') == $state ) return true;
				
		$service = new WorkflowService($object_it->object);
		try {
			$service->moveToState( $object_it, $state, '', array(), false );
		}
 		catch( Exception $e ) {
			Logger::getLogger('System')->error($e->getMessage());
		}
 		return true;
 	}

 	function getObject()
 	{
 		return null;
 	}
 	
 	function getDisplayName()
 	{
 		return text(2079);
 	}
}
