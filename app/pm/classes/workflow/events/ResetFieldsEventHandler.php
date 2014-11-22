<?php

include_once "WorklfowMovementEventHandler.php";

class ResetFieldsEventHandler extends WorklfowMovementEventHandler
{
	function handle( $object_it )
	{
		$parms = array();
		
		$reset_fields_it = getFactory()->getObject('TransitionResetField')->getByRef('Transition', $object_it->get('LastTransition'));
		
		while ( !$reset_fields_it->end() )
		{
			$parms[$reset_fields_it->get('ReferenceName')] = '';
			$reset_fields_it->moveNext();
		}
		
		if ( count($parms) < 1 ) return;
		
		$object_it->object->modify_parms( $object_it->getId(), $parms );
	}
}