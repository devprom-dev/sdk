<?php

include_once "EmailNotificatorHandler.php";

class ChangeRequestHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		switch ( $action )
		{
			case 'add':
				$result = $this->getProject($object_it)->getLeadIt()->idsToArray();
				break;
		}
		
		return $result;
	}	
	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
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
					if ( $object_it->get('Owner') > 0 )
					{
						array_push($result, $object_it->get('Owner'));
					}
				}
				break;

			case 'add':
				if ( $object_it->get('Owner') > 0 )
				{
					array_push($result, $object_it->get('Owner'));
				}
				break;
		}
		
		return $result;
	}	
}  
