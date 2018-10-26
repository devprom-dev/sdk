<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_cofeedbackissue.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

 require_once (dirname(__FILE__).'/c_feedbackask.php');
 require_once (dirname(__FILE__).'/c_cofeedback_base.php');

 ////////////////////////////////////////////////////////////////////////////
 class CoFeedbackIssue extends FeedbackIssue
 {
 	function getProxy()
 	{
 		return new CoFeedbackBase;
 	}
 	
 	function execute()
	{
		global $_REQUEST, $model_factory, $project_it, 
			   $user_it, $_FILES, $controller;

		$issue_it = parent::execute();
		
		// send user notification about his issue
		if ( $user_it->count() > 0 )
		{
			 $mail = new HtmlMailBox;
			
			 $settings = $model_factory->getObject('cms_SystemSettings');
			 $settings_it = $settings->getAll();
			
			 $lead_it = $project_it->getLeadIt();
			
			 $body = str_replace('%1', $project_it->getDisplayName(), text('procloud490'));
			 $body = str_replace('%2', $issue_it->get('Caption'), $body);
			 $body = str_replace('%3', $controller->getGlobalUrl($issue_it), $body);
			 $body = str_replace('%4', $lead_it->getDisplayName(), $body);
			 $body = str_replace('%5', Controller::getProductUrl($project_it), $body);
			
			 if ( $project_it->get('CodeName') == 'procloud' )
			 {
			 	$body .= '<br/>twitter: @devprom';
			 }
			 
			 $mail->setFrom($lead_it->getDisplayName().' <'.$lead_it->get('Email').'>');
			 $mail->setSubject($project_it->get('CodeName').': '.text('procloud419'));
			
			 $mail->appendAddress($user_it->get('Email'));
			 $mail->setBody($body);
			 $mail->send();
		 }
		 
		 return $issue_it;
	}
 }
 
?>