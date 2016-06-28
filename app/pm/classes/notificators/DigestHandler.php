<?php

include_once "EmailNotificatorHandler.php";

class DigestHandler extends EmailNotificatorHandler
{
	private $recipient_it = null;
	private $from_date = '';
	
	function setRecipient( $recipient_it )
	{
		$this->recipient_it = $recipient_it->copy();
	}
	
	function setFromDate( $date )
	{
		$this->from_date = $date;
	}
	
	function getTemplate()
	{
		return 'digest.twig';
	}
	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		return str_replace( '%1', getSession()->getProjectIt()->get('CodeName'), text(965) );
	}
	
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		return array(
				$this->recipient_it->getId()
		);
	}	
	
	function IsParticipantNotified( $participant_it )
	{
		return $participant_it->getId() == $this->recipient_it->getId();
	}
	
	function getRenderParms($action, $object_it, $prev_object_it)
	{
		$app_url = EnvironmentSettings::getServerUrl().getSession()->getApplicationUrl();
		
		return array (
			'user' => $this->recipient_it->getDisplayName(),
			'log_url' => $app_url.'project/log?participant=all&mode=log&start='.urlencode($this->from_date),
			'profile_url' => $app_url.'profile',
			'dates' => $this->getChanges($object_it),
			'fields' => array(0)
		);
	}
	
	function getChanges( $log_it )
	{
		$dates = array();
		
		while ( !$log_it->end() )
		{
			$date_formatted = getSession()->getLanguage()->getDateFormatted($log_it->get('ChangeDate'));
			$anchor_it = $log_it->getObjectIt();

			$dates[$date_formatted][$log_it->get('AuthorName')][] = array (
				'action' => $log_it->get('ChangeKind'),
				'entity' => $anchor_it->object->getDisplayName(),
				'title' => $log_it->getHtmlValue($log_it->getHtmlDecoded('Caption')),
				'content' => $log_it->getHtmlValue($log_it->getHtmlDecoded('Content')),
				'time' => getSession()->getLanguage()->getTimeFormatted($log_it->get('RecordCreated'))
			);
			
			$log_it->moveNext();
		}
		
		return $dates;
	}
}  
