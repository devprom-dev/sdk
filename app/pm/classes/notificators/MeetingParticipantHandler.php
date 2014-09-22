<?php

include_once "EmailNotificatorHandler.php";

class MeetingParticipantHandler extends EmailNotificatorHandler
{
 	function IsAccepted( $object_it, $prev_object_it )
 	{
 		return $object_it->get('Accepted') != $prev_object_it->get('Accepted') 
			&& $object_it->get('Accepted') == 'Y';
 	}
 	
 	function IsRejected( $object_it, $prev_object_it )
 	{
 		return $object_it->get('Rejected') != $prev_object_it->get('Rejected') 
			&& $object_it->get('Rejected') == 'Y';
 	}

	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory;
		
		$result = array();
		
		switch( $action )
		{
			case 'add':
				array_push($result, $object_it->get('Participant'));
				break;
				
			case 'delete':
				array_push($result, $prev_object_it->get('Participant'));
				break;

			case 'modify':
				$notify = $this->IsRejected( $object_it, $prev_object_it )
					|| $this->IsAccepted( $object_it, $prev_object_it ); 
				
				if ( $notify )
				{
					$meeting_it = $object_it->getRef('Meeting');
					$part_it = $meeting_it->getParticipanceIt();
					
					while ( !$part_it->end() )
					{
						array_push($result, $part_it->getId());
						$part_it->moveNext();
					}
				}
				break;
		}
		
		return $result;
	}	
 	
	function getPreBody( $action, $object_it, $prev_object_it )
	{
		global $project_it;
		
		$text = '';
		
		switch( $action )
		{
			case 'add':
				$meeting_it = $object_it->getRef('Meeting');
				$url = $this->getObjectItUid( $meeting_it );
				
				$text = text(257);
				$text .= '<br/><a href="'.$url.'">'.$url.'</a><br/><br/>';
				
				return $text;
				
			case 'delete':
				$meeting_it = $prev_object_it->getRef('Meeting');
				$url = $this->getObjectItUid( $meeting_it );
				
				$text = text(412);
				$text .= '<br/><a href="'.$url.'">'.$url.'</a><br/><br/>';
				
				return $text;

			case 'modify':
				$part_it = $object_it->getRef('Participant');
				
				if ( $this->IsAccepted( $object_it, $prev_object_it ) )
				{
					return str_replace('%1', $part_it->getDisplayName(), text(258)).'<br/>'.'<br/>';
				}
				
				if ( $this->IsRejected( $object_it, $prev_object_it ) )
				{
					return str_replace('%1', $part_it->getDisplayName(), text(259)).'<br/>';
				}

				break;
		}
		
		return '';
	}

	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		$meeting_it = $object_it->getRef('Meeting');

		$body = translate('Тема').': '.$meeting_it->get('Subject').'<br/>';
		$body .= translate('Дата').': '.$meeting_it->getDateFormat('MeetingDate').'<br/>';
		$body .= translate('Время').': '.$meeting_it->get('MeetingTime').'<br/>';
		$body .= translate('Место').': '.$meeting_it->get('Location').'<br/>';
		$body .= translate('Повестка').': '.'<br/>'.$meeting_it->get_native('Agenda').'<br/>';
		
		return $body;
	}

	function IsParticipantNotified( $participant_it )
	{
		return true;
	}
}  
