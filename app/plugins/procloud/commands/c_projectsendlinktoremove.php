<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_projectsendlinktoremove.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */
 
 class ProjectSendLinkToRemove extends CommandForm
 {
 	function validate()
 	{
		global $_REQUEST, $model_factory, $user_it;

		$this->project = $model_factory->getObject('pm_Project');

		// proceeds with validation
		$this->checkRequired( array('codename') );

		if ( $user_it->getId() < 1 )
		{
			return false;
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $_REQUEST, $model_factory, $user_it;

		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

		// get the project
		$project_it = $this->project->getExact($_REQUEST['codename']);
		if ( $project_it->count() < 1 )
		{
			$this->replyError( 
				$this->getResultDescription( 2 ) );
				
			return;
		}
		
		$model_factory->enableVpd(false);
		
		// check if current user is a lead
		$lead_it = $project_it->getLeadIt();
		$found_lead = false;
		
		while ( !$lead_it->end() )
		{
			if ( $lead_it->get('SystemUser') == $user_it->getId() )
			{
				$found_lead = true;
				break;
			}
			$lead_it->moveNext();
		}
		
		if ( !$found_lead )
		{
			$this->replyError( 
				$this->getResultDescription( 3 ) );
				
			return;
		}
		
		// send notification with remove link
   		$mail = new Mailbox;
		$body = text('procloud289');
		
		$body = str_replace('%1', 
			$lead_it->getDisplayName(), $body);
			
		$body = str_replace('%2', 
			$project_it->getDisplayName(), $body);

		$body = str_replace('%3', 
			_getServerUrl().'/room/projectremove?key='.$project_it->getRemoveKey(), $body);

   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
   		$mail->setSubject( text('procloud290') );
   		$mail->appendAddress($lead_it->get('Email'));
   		$mail->setBody($body);
		$mail->send();

		$this->replySuccess( 
			$this->getResultDescription( 4 ) );
	}

	function getResultDescription( $result )
	{
		switch($result)
		{
			case 2:
				return text('procloud286');
				
			case 3:
				return text('procloud287');

			case 4:
				return text('procloud288');

			default:
				return parent::getResultDescription( $result );
		}
	}
 }
 
?>