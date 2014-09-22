<?php

include_once "EmailNotificatorHandler.php";

class ChangeRequestHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory, $part_it, $project_it;
		
		$result = array();
		
		switch ( $action )
		{
			case 'add':
				$lead_it = $project_it->getLeadIt();
	
				while ( !$lead_it->end() )
				{
					array_push($result, $lead_it->getId());
					$lead_it->moveNext();
				}
	
				if ( $object_it->get('Owner') > 0 )
				{
					array_push($result, $object_it->get('Owner'));
				}
				break;
				
			case 'modify':
				$prev_state = $prev_object_it->getStateName();
				$state = $object_it->getStateName(); 

				if ( $prev_state <> $state )
				{
					if ( $object_it->get('Owner') > 0 )
					{
						array_push($result, $object_it->get('Owner'));
					}
				}
				break;
		}
		
		return $result;
	}	
	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory, $part_it, $project_it;
		
		$result = array();
		
		switch ( $action )
		{
			case 'modify':
				$prev_state = $prev_object_it->getStateName();
				$state = $object_it->getStateName(); 

				if ( $prev_state <> $state )
				{
					if ( $object_it->get('Author') > 0 )
					{
						array_push($result, $object_it->get('Author'));
					}
				}
				break;
		}
		
		return $result;
	}	
}  
