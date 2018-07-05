<?php

namespace Devprom\CommonBundle\Service\Project;
use \Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
		
		$lang = strtolower($this->session->getLanguageUid());
		$this->email_template = 'CommonBundle:Emails/'.$lang.':invite.html.twig';
	}
	
	public function inviteByEmails( $emails, $projectRoleId = '' )
	{
		if ( !is_array($emails) ) {
			$emails = array_filter(
					preg_split('/[,\s;\r\n]/', $emails),
					function($value) {
							return $value != '' && filter_var(trim($value), FILTER_VALIDATE_EMAIL) !== false;
					}
	        );
		}
		
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
				$this->addParticipant(
				    $this->session->getProjectIt(),
                    $user_it,
                    getFactory()->getObject('ProjectRole')->getRegistry()->Query(
                        array(
                            new \FilterInPredicate($projectRoleId == '' ? '-1' : $projectRoleId)
                        )
                    )
                );
			}
			else
			{
				getFactory()->getObject('Invitation')->add_parms(
                    array (
                        'Project' => $this->session->getProjectIt()->getId(),
                        'Author' => $this->session->getUserIt()->getId(),
                        'ProjectRole' => $projectRoleId,
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
		if ( !getFactory()->getAccessPolicy()->can_create($user) ) throw new NotFoundHttpException(text(2151));
		
		$user_it = $user->getRegistry()->Query(
			array (
				new \FilterAttributePredicate('Email', $email)
			)
		);
		if ( $user_it->getId() > 0 ) return $user->getEmptyIterator();

		$invite_it = getFactory()->getObject('Invitation')->getRegistry()->Query(
			array (
				new \FilterAttributePredicate('Addressee', $email)
			)
		);
		if ( $invite_it->getId() < 1 ) return $user->getEmptyIterator();

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
		
		$participant_it = $this->addParticipant(
		    $invite_it->getRef('Project'),
            $user_it,
            getFactory()->getObject('ProjectRole')->getRegistry()->Query(
                array(
                    new \FilterInPredicate($invite_it->get('ProjectRole') == '' ? '-1' : $invite_it->get('ProjectRole') )
                )
            )
        );
		
		$invite_it->delete();
		
		return $participant_it;
	}
	
	public function addParticipant( $project_it, $user_it, $role_it = null )
	{
		$it = getFactory()->getObject('Participant')->getRegistry()->Query(
				array(
						new \FilterAttributePredicate('SystemUser', $user_it->getId()),
						new \FilterAttributePredicate('Project', $project_it->getId())
				)
		);
		
		if ( $it->getId() == '' ) {
            $participant = getFactory()->getObject('Participant');
            $participant_it = $participant->getExact(
                $participant->add_parms(
                    array (
                        'SystemUser' => $user_it->getId(),
                        'Project' => $project_it->getId(),
                        'VPD' => $project_it->get('VPD'),
                        'NotificationTrackingType' => $user_it->get('NotificationTrackingType'),
                        'NotificationEmailType' => $user_it->get('NotificationEmailType')
                    )
                )
            );
        }
        else {
            $participant_it = $it;
        }
		

		if ( !is_object($role_it) || $role_it->getId() == '' ) {
            $role_it = getFactory()->getObject('ProjectRole')->getRegistry()->Query(
                array(
                    new \FilterVpdPredicate($project_it->get('VPD')),
                    new \FilterAttributePredicate('ReferenceName', array('lead','developer'))
                )
            );
        }
		
		getFactory()->getObject('ParticipantRole')->getRegistry()->Merge(
            array (
                'Participant' => $participant_it->getId(),
                'ProjectRole' => $role_it->getId(),
                'Capacity' => '1',
                'Project' => $project_it->getId(),
                'VPD' => $project_it->get('VPD')
            ),
            array('Participant', 'ProjectRole')
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
   		$mail->setFromUser($this->session->getUserIt());

   		$mail->send();
	}
	
	private $controller = null;
	private $session = null;
	private $email_template = ''; 
}