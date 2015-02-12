<?php

include_once "EmailNotificatorHandler.php";

class TaskHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		switch ( $action )
		{
			case 'add':
				return getSession()->getProjectIt()->getLeadIt()->idsToArray();
		}
		return array();
	}	

	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		if ( $object_it->get('Assignee') > 0 ) $result[] = $object_it->get('Assignee');
		
		return $result;
	}
}  
