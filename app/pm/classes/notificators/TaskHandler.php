<?php

include_once "EmailNotificatorHandler.php";

class TaskHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		switch ( $action )
		{
			case 'add':
				$lead_it = getSession()->getProjectIt()->getLeadIt();
	
				while ( !$lead_it->end() )
				{
					array_push($result, $lead_it->getId());
					
					$lead_it->moveNext();
				}
	
				
				break;
		}

		if ( $object_it->get('Assignee') > 0 )
		{
			array_push($result, $object_it->get('Assignee'));
		}
		
		return $result;
	}	
}  
