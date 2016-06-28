<?php

include_once "WorklfowMovementEventHandler.php";

class ApplyBusinessActionsEventHandler extends WorklfowMovementEventHandler
{
	function handle( $object_it )
	{
		$state_it = $object_it->getStateIt();
		if ( $state_it->getId() < 1 ) return;
		
 		$action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('State', $state_it->getId()),
 				)
 		);
		$action = getFactory()->getObject('StateBusinessAction');
 		while ( !$action_it->end() )
 		{
	 		$rule_it = $action_it->getRef('ReferenceName', $action);
	 		if ( is_object($rule_it) && $rule_it->checkType('BusinessActionWorkflow') )
			{
				try {
					Logger::getLogger('System')->info('Applying system action: '.$rule_it->getDisplayName());
					$rule_it->apply( $object_it );
				}
				catch( Exception $e ) {
					Logger::getLogger('System')->error(
						'Unable complete system action "'.$rule_it->getDisplayName().'"'.PHP_EOL.
						$e->getMessage().PHP_EOL.
						$e->getTraceAsString()
					);
				}
			}
	 		$action_it->moveNext();
 		}
	}
}