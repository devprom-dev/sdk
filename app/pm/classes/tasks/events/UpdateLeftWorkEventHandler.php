<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/events/WorklfowMovementEventHandler.php";

class UpdateLeftWorkEventHandler extends WorklfowMovementEventHandler
{
	function readyToHandle()
	{ 
		return $this->getObjectIt()->object instanceof Task;  
	}
	
	function handle( $object_it )
	{
		if ( $object_it->get('ChangeRequest') == '' ) return;
		if ( $object_it->getStateIt()->get('IsTerminal') != 'Y' ) return;
		if ( !getSession()->getProjectIt()->getMethodologyIt()->getEstimationStrategy() instanceof EstimationHoursStrategy ) return;
				
		$request_it = $object_it->getRef('ChangeRequest');
		
		$request_it->object->modify_parms(
				$request_it->getId(),
				array (
						'EstimationLeft' => max(0, $request_it->get('EstimationLeft') - $object_it->get('Planned'))
				)
		); 
	}
}