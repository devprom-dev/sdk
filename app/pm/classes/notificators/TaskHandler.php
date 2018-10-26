<?php

include_once "EmailNotificatorHandler.php";

class TaskHandler extends EmailNotificatorHandler
{
	function getUsers( $object_it, $prev_object_it, $action )
	{
		$result = array();
		
        switch ( $action ) {
            case 'modify':
                if ( $prev_object_it->getStateIt()->getId() != $object_it->getStateIt()->getId() ) {
                    if ( $object_it->get('Author') > 0 ) {
                        array_push($result, $object_it->get('Author'));
                    }
                    if ( $object_it->get('Assignee') > 0 ) {
                        array_push($result, $object_it->get('Assignee'));
                    }
                }
                if ( $prev_object_it->get('Assignee') != $object_it->get('Assignee') ) {
                    if ( $object_it->get('Assignee') > 0 ) {
                        array_push($result, $object_it->get('Assignee'));
                    }
                }
                break;

            case 'add':
                if ( $object_it->get('Assignee') > 0 ) $result[] = $object_it->get('Assignee');
                break;
        }

		return $result;
	}
}  
