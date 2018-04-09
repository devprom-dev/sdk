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
				$result = $this->getProject($object_it)->getLeadIt()->idsToArray();
				break;
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
				if ( $object_it->get('Author') > 0 ) {
					array_push( $result, $object_it->get('Author') );
				}
				break;
		}

        if ( $object_it->get('Owner') != '' ) {
            array_push($result, $object_it->get('Owner'));
        }

		return $result;
	}	
}  
