<?php

include_once "EmailNotificatorHandler.php";

class QuestionHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		switch( $action )
		{
			case 'add':
				$result = getSession()->getProjectIt()->getLeadIt()->idsToArray();
				break;
		}

		if ( $object_it->get('Owner') != '' )
		{
		    array_push($result, $object_it->get('Owner'));
		}
		
		return $result;
	}	
 	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		$result = array();
		
		switch( $action )
		{
			case 'add':
				break;
				
			default:
				if ( $object_it->get('Author') > 0 )
				{
					array_push( $result, $object_it->get('Author') );
				}
				break;
		}
		
		return $result;
	}	
}  
