<?php

use Devprom\CommonBundle\Service\Emails\RenderService;
include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class ScrumReportedEvent extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
	    if ( $object_it->object->getEntityRefName() != 'pm_Scrum' ) return;
	    if ( $kind != TRIGGER_ACTION_ADD ) return;

	    $render_service = new RenderService(
	    		getSession(), SERVER_ROOT_PATH."plugins/scrum/views/Emails"
		);
	    
   		$mail = new \HtmlMailbox;
   		
   		$mail->setFromUser(getSession()->getUserIt());
   		foreach( getSession()->getProjectIt()->getParticipantIt()->fieldToArray('Email') as $email )
   		{
   			$mail->appendAddress($object_it->get('Email'));
   		}
   		$mail->setSubject(text('scrum2'));
   		$mail->setBody($render_service->getContent('scrum-report.twig', 
   				array (
	   				'yesterday' => $object_it->getHtmlDecoded('WasYesterday'),
   					'today' => $object_it->getHtmlDecoded('WhatToday'),
   					'blockers' => $object_it->getHtmlDecoded('CurrentProblems')
   				)
   		));
   		
   		$mail->send();
	}
}