<?php

include_once "WorklfowMovementEventHandler.php";

class ApplyBusinessActionsEventHandler extends WorklfowMovementEventHandler
{
	function handle( $object_it )
	{
		$state_id = $object_it->getStateIt()->getId();
		
		if ( $state_id < 1 ) return;
		
 		$action_it = getFactory()->getObject('StateAction')->getRegistry()->Query(
 				array (
 						new FilterAttributePredicate('State', $state_id),
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