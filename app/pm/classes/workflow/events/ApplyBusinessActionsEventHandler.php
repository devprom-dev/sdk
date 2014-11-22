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

 		while ( !$action_it->end() )
 		{
	 		$rule_it = $action_it->getRef('ReferenceName', getFactory()->getObject('StateBusinessAction'));

	 		if ( is_object($rule_it) ) $rule_it->apply( $object_it ); 

	 		$action_it->moveNext();
 		}
	}
}