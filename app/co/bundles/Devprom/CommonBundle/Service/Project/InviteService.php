<?php

namespace Devprom\CommonBundle\Service\Project;

include_once SERVER_ROOT_PATH."pm/classes/participants/Invitation.php";
include_once SERVER_ROOT_PATH."pm/classes/participants/Participant.php";
include_once SERVER_ROOT_PATH."pm/classes/participants/ParticipantRole.php";
include_once SERVER_ROOT_PATH."pm/classes/participants/ProjectRole.php";
include_once SERVER_ROOT_PATH."cms/c_mail.php";

class InviteService
{
	public function __construct( $controller, $session )
	{
		$this->controller = $controller;
		$this->session = $session;
		
		$lang = strtolower($this->session->getLanguage()->getLanguage());
		$this->email_template = 'CommonBundle:Emails/'.$lang.':invite.html.twig';
	}
	
	public function inviteByEmails( $emails )
	{
		if ( !getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Participant')) ) return;

		foreach( $emails as $email )
		{
			$email = trim(strtolower($email));
			
			$user_it = getFactory()->getObject('User')->getRegistry()->Query(
					array (
							new \FilterAttributePredicate('Email', $email)
					)
			);
			
			if ( $user_it->getId() > 0 )
			{
				$this->addParticipant($this->session->getProjectIt(), $user_it); 
			}
			else
			{
				getFactory()->getObject('Invitation')->add_parms(
						array (
								'Project' => $this->session->getProjectIt()->getId(),
								'Author' => $this->session->getUserIt()->getId(),
								'Addressee' => $email 
						)
				);
				
				$this->sendEmail( $email );
			}
		}
	}
	
	public function applyInvitation( $email )
	{
		$email = trim(strtolower($email));
		
		$user = getFactory()->getObject('User');
		
		if ( !getFactory()->getAccessPolicy()->can_create($user) ) return $user->getEmptyIterator();
		
		$user_it = $user->getRegistry()->Query(
				array (
						new \FilterAttributePredicate('Email', $email)
				)
		);
		
		if ( $user_it->getId() > 0 )
		{
			return getFactory()->getObject('Participant')->getRegistry()->Query(
					array (
							new \FilterAttributePredicate('SystemUser', $user_it->getId())
					)
			);
		}
		
		$invite_it = getFactory()->getObject('Invitation')->getRegistry()->Query(
				array (
						new \FilterAttributePredicate('Addressee', $email)
				)
		);
		
		if ( $invite_it->getId() < 1 )
		{
			return getFactory()->getObject('Participant')->getRegistry()->Query(
					array (
							new \FilterAttributePredicate('SystemUser', $user_it->getId())
					)
			);
		}
		
		$parts = preg_split('/@/', $email);
		
		$login = $parts[0];

		$user_it = $user->getRegistry()->Query(
				array (
						new \FilterAttributePredicate('Login', $login)
				)
		);
		
		if ( $user_it->getId() > 0 ) $login = $email;
		
		$user->setNotificationEnabled(false);
		
		$user_it = $user->getExact(
				$user->add_parms(
						array (
								'Caption' => $login,
								'Login' => $login,
								'Email' => $email,
								'Password' => $login,
								'Language' => getFactory()->getObject('cms_SystemSettings')->getAll()->get('Language')
						)
				)
		);
		
		$participant_it = $this->addParticipant($invite_it->getRef('Project'), $user_it);
		
		$invite_it->delete();
		
		return $participant_it;
	}
	
	protected function addParticipant( $project_it, $user_it )
	{
		$it = getFactory()->getObject('Participant')->getRegistry()->Query(
				array(
						new \FilterAttributePredicate('SystemUser', $user_it->getId()),
						new \FilterAttributePredicate('Project', $project_it->getId())
				)
		);
		
		if ( $it->getId() > 0 ) return;
		
		$participant = getFactory()->getObject('Participant');
		$participant_it = $participant->getExact(
				$participant->add_parms(
					array (
							'SystemUser' => $user_it->getId(),
							'Project' => $project_it->getId(),
							'VPD' => $project_it->get('VPD'),
							'Notification' => $project_it->getDefaultNotificationType()
					)
			 	)
		);
		
		getFactory()->getObject('ParticipantRole')->add_parms(
				array (
						'Participant' => $participant_it->getId(),
						'ProjectRole' => getFactory()->getObject('ProjectRole')->getRegistry()->Query(
												array(
														new \FilterVpdPredicate($project_it->get('VPD')),
														new \FilterAttributePredicate('ReferenceName', array('lead','developer'))
												)
											)->getId(),
						'Capacity' => '1',
						'Project' => $project_it->getId(),
						'VPD' => $project_it->get('VPD')
				)
		);
		
		return $participant_it;
	}
	
	protected function sendEmail( $email )
	{
    	$content = $this->controller->render( $this->email_template, 
	    			array (
		    			'sender' => $this->session->getUserIt()->getHtmlDecoded('Caption'),
		    			'project' => $this->session->getProjectIt()->getHtmlDecoded('Caption'),
		    			'url' => \EnvironmentSettings::getServerUrl().'/join-project?email='.$email
	    			)
    		)->getContent();
    	
   		$mail = new \HtmlMailbox;
   		
   		$mail->appendAddress($email);
   		$mail->setBody($content);
   		$mail->setSubject( text(1863) );
   		$mail->setFrom($this->session->getUserIt()->get('Email'));

   		$mail->send();
	}
	
	private $controller = null;
	private $session = null;
	private $email_template = ''; 
}