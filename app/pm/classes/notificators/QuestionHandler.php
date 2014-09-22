<?php

include_once "EmailNotificatorHandler.php";

class QuestionHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $project_it;
		
		$result = array();
		
		switch( $action )
		{
			case 'add':
				$lead_it = $project_it->getLeadIt();
				
				while ( !$lead_it->end() )
				{
					array_push($result, $lead_it->getId());
					
					$lead_it->moveNext();
				}
				
				break;
				
			default:
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
 	
	function getPreBody( $action, $object_it, $prev_object_it ) 
	{
		global $model_factory;
		
		if ( $action != 'add' )
		{
			return '';
		}
		
		$body = text(332);
		
		$body = str_replace('%1', getSession()->getUserIt()->getDisplayName(), $body);
		$body = str_replace('%2', $object_it->getHtml('Content'), $body);
		$body = str_replace('%3', $this->getObjectItUid( $object_it ), $body);
		
		return $body;
	}
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		if ( $action != 'add' )
		{
			return '';
		}
		
		return ' ';
	}
}  
