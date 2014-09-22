<?php

include_once "EmailNotificatorHandler.php";

class UserMailHandler extends EmailNotificatorHandler
{
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		$result = array();

		if ( $action != 'add' ) return $result;
		
		array_push( $result, $object_it->get('ToParticipant') );

		return $result;
	}	

	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		return $object_it->getHtml('Content');
	}

	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		global $project_it;
		return '['.$project_it->get('CodeName').'] '.$object_it->get('Subject');
	}

	function IsParticipantNotified( $participant_it )
	{
		return true;
	}
 }  
