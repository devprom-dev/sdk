<?php

include_once "BusinessAction.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestModelExtendedBuilder.php";

class TaskBusinessActionResolveIssue extends BusinessAction
{
 	function getId()
 	{
 		return '1327269011';
 	}
	
	function apply( $object_it )
 	{
 		global $model_factory;
 		
		if ( !getSession()->getProjectIt()->getMethodologyIt()->HasTasks() ) return true;
		
		if ( $object_it->get('ChangeRequest') == '' ) return true;
		
		getSession()->addBuilder( new RequestModelExtendedBuilder() );
		
		$request_it = $object_it->getRef('ChangeRequest');
 		
		$task_it = $request_it->getRef('OpenTasks');

		// if there are no open tasks then resolve an issue
		if ( $task_it->end() )
		{
			$resolution = translate('Результат').': '.$object_it->get('Result');

			$terminals = $request_it->object->getTerminalStates();
			
			$transition_it = $request_it->getTransitionTo( $terminals[0] );

			if ( $transition_it->getId() > 0 )
			{
				$request_it->modify (
					array( 	'State' => $terminals[0],
					 	'Transition' => $transition_it->getId(),
						'TransitionComment' => $resolution ) 
				);
			}
		}
 		
 		return true;
 	}

 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_Task');
 	}
 	
 	function getDisplayName()
 	{
 		return text(1168);
 	}
}
