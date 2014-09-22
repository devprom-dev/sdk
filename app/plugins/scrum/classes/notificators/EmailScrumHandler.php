<?php

class ScrumHandler
{
  	function getSender( $object_it, $action ) 
 	{
 	    $part_it = getSession()->getParticipantIt();
 	    
		return $part_it->get('Caption').' <'.$part_it->get('Email').'>';
 	}
 	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		return '['.getSession()->getProjectIt()->get('CodeName').'] '.$subject;
	}
	
	function getMailBox() 
	{
		return new HtmlMailBox;
	}
	
	function IsParticipantNotified( $participant_it )
	{
		global $model_factory;
		
		$notification = $model_factory->getObject('Notification');
		$notification_type = $notification->getType( $participant_it );
							
		return $notification_type == 'all' || $notification_type == 'system';
	}     
	
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $project_it;
		
		$result = array();
		
		$part_it = $project_it->getParticipantIt();

		while( !$part_it->end() )
		{
			array_push($result, $part_it->getId());
			$part_it->moveNext();
		}
		
		return $result;
	}	
 	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		$body = text(254).': '.Chr(10).Chr(10);
		
		$body .= $object_it->get_native('WasYesterday').Chr(10).Chr(10);
		
		$body .= text(255).': '.Chr(10).Chr(10);
		
		$body .= $object_it->get_native('WhatToday').Chr(10).Chr(10);
		
		$body .= text(256).': '.Chr(10).Chr(10);
		
		$body .= $object_it->get_native('CurrentProblems').Chr(10).Chr(10);
		
		return $body;
	}
}  