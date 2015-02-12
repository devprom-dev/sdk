<?php

use Devprom\CommonBundle\Service\Emails\RenderService;

include_once SERVER_ROOT_PATH.'cms/c_mail.php';
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";
include "EmailNotificatorHandler.php";
include "CommentHandler.php";
include "ChangeRequestHandler.php";
include "QuestionHandler.php";
include "BlogPostHandler.php";
include "WikiHandler.php";
include "TaskHandler.php";
include "DigestHandler.php";

class EmailNotificator extends ObjectFactoryNotificator
{
 	var $handlers, $common_handler, $notification_reason;
	
	function __construct() 
	{
		parent::__construct();
		
		$this->common_handler = new EmailNotificatorHandler;
		
		$this->handlers = array(
			'Comment' => new CommentHandler(),
			'pm_ChangeRequest' => new ChangeRequestHandler(),
			'pm_Question' => new QuestionHandler(),
			'BlogPost' => new BlogPostHandler(),
			'WikiPage' => new WikiHandler(),
			'pm_Task' => new TaskHandler(),
			'ObjectChangeLog' => new DigestHandler()
		);
		
		$this->notification_reason = array();
	}
 	
	function add( $object_it ) 
	{
		$this->process( 'add', $object_it, $object_it );
	}

 	function modify( $prev_object_it, $object_it ) 
	{
		$this->process( 'modify', $object_it, $prev_object_it );
	}

 	function delete( $object_it ) 
	{
		$this->process( 'delete', $object_it, $object_it );
	}
	 
 	public function & getHandler( $object_it ) 
 	{
	    if ( $object_it->object->getClassName() == 'WikiPage' && !is_a( $object_it->object, 'WikiPage' ) )
	    {
	        return $this->common_handler; 
	    }
		    
		return array_key_exists($object_it->object->getClassName(), $this->handlers) 
					? $this->handlers[$object_it->object->getClassName()] : $this->common_handler; 
 	}
	
	public function sendMail( $action, $object_it, $prev_object_it ) 
	{
		$queues = array();
		
	    $render_service = new RenderService(
	    		getSession(), SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails"
		);
		
		$from = $this->getSender($object_it, $action);
		if( $from == '' )
		{
			$this->info('Sender is undefined');
			return $queues;
		}
		
		$recipients = $this->getRecipientArray($object_it, $prev_object_it, $action);
		if( count($recipients) < 1 )
		{
			$this->info('Recipients are unknown');
			return $queues;
		} 

		$keys = array_keys($recipients);
		for($i = 0; $i < count($keys); $i++) 
		{
			$recipient = $recipients[$keys[$i]];
			
			$mail = new HtmlMailBox();
			$mail->setFrom($from);
			
			$mail->appendAddress( $this->getAddress($recipient) );

			$mail->setSubject( $this->getSubject( $object_it, $prev_object_it, $action, $recipient ) );
	
			$parms = $this->getRenderParms($action, $object_it, $prev_object_it, $recipient);
	   		$mail->setBody($render_service->getContent($parms['template'], $parms));
			
			$queues[] = $mail->send();
		} 
		
		return $queues;
	}
	
	protected function process( $action, $object_it, $prev_object_it ) 
	{
		global $model_factory, $_REQUEST;

		if ( !is_object($object_it->object->entity) ) return;

		switch ( $object_it->object->entity->get('ReferenceName') )
		{ 
			case 'pm_Participant' :
			case 'pm_Project' :
			case 'pm_Meeting':
			case 'pm_MeetingParticipant' :
			case 'pm_ChangeRequest' :
			case 'pm_Artefact' : 
			case 'Comment' :
			case 'pm_Question':
			case 'BlogPost':
				$this->sendMail($action, $object_it, $prev_object_it);
				break;

			case 'pm_Task':
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
				{
					$this->sendMail($action, $object_it, $prev_object_it);
				}
				break;
				
			case 'WikiPage' :
				$type_it = getFactory()->getObject('WikiType')->getExact($object_it->get('ReferenceName'));
				
				switch ( $type_it->get('ReferenceName') )
				{
					case 'Requirements':
						if ( $action == 'modify' )
						{
							$this->sendMail($action, $object_it, $prev_object_it);
						}
						break;

					default:
						if ( $action != 'delete' )
						{
							$this->sendMail($action, $object_it, $prev_object_it);
						}
				}
				break;

			default:
				return;
		}
	}
		
	protected function getAddress( $recipient )
	{ 
		if ( is_object($recipient) )
		{
			return $recipient->getDisplayName().' <'.$recipient->get('Email').'>';
		}
		else
		{
			return $recipient;
		}
	}
	
	protected function addRecipient( $object_it, &$emails )
	{
		$title = $object_it->get('Caption');
		if ( strpos($title, ",") !== false )
		{
			$title = '"'.trim($object_it->get('Caption'),'"').'"';
		}
		return $emails[] = $title.' <'.$object_it->get('Email').'>';
	}
	
	protected function getRenderParms( $action, $object_it, $prev_object_it, $recipient )
	{
		$handler = $this->getHandler( $object_it );
		return array_merge(
				array (
						'template' => $handler->getTemplate(),
						'reason' => $this->notification_reason[$recipient]
				),
				$handler->getRenderParms($action, $object_it, $prev_object_it)
		); 
	}
	
	protected function getSender( $object_it, $action )
	{
		$part_it = getSession()->getParticipantIt();
		$user_it = getSession()->getUserIt();
		
		if ( $part_it->get('Email') != '' )
		{
			return $this->quoteEmail($part_it->get('Caption')).' <'.$part_it->get('Email').'>';
		}
		else
		{
			if ( $user_it->get('Email') != '' )
			{
		 		return $this->quoteEmail($user_it->get('Caption')).' <'.$user_it->get('Email').'>';
			}
			else
			{
				return getFactory()->getObject('cms_SystemSettings')->getAll()->getHtmlDecoded('AdminEmail');
			}
		}
	}

	protected function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory;
		
		$project_it = getSession()->getProjectIt();
		
		$part_it = getSession()->getParticipantIt();
		
		$user_it = getSession()->getUserIt();
		
		$notification = $model_factory->getObject('Notification');
		
		$handler = $this->getHandler( $object_it );

		$participants = $handler->getParticipants( $object_it, $prev_object_it, $action );

		$users = $handler->getUsers( $object_it, $prev_object_it, $action );
		
		// include participants who wants to receive all notifications
		$participant = $model_factory->getObject('Participant');
		
		$participant->addFilter( new ParticipantActivePredicate() );
		
		$it = $participant->getAll();
		
		while ( !$it->end() )
		{
			if ( $notification->getType( $it ) != 'all' )
			{
			    $it->moveNext();
			    
			    continue;
			}
			
			array_push( $participants, $it->getId() );
			
			$it->moveNext();
		}

		// make email addresses
		$emails = array();

		if ( is_object($part_it) )
		{
			$current_part_id = $part_it->getId();
			$current_user_id = $part_it->get('SystemUser');
		}
		else if ( is_object($user_it) )
		{
			$current_user_id = $user_it->getId();
		}

		// process users
		$user = $model_factory->getObject('cms_User');
		
		if ( count($users) > 0 )
		{
		    $systemuser_it = $user->getExact($users);
		}
		else
		{
		    $systemuser_it = $user->getEmptyIterator();
		}
		
		while( !$systemuser_it->end() )
		{
			// exclude a user who initiated the notification
			if ( $systemuser_it->getId() == $current_user_id && $current_user_id != '' )
			{
				$this->info($systemuser_it->getDisplayName().' skipped as current');
				$systemuser_it->moveNext();
				continue;
			}
			
			// check if user is a prticipant
			$it = $project_it->getParticipantForUserIt( $systemuser_it );
			if ( $it->count() < 1 )
			{
				$systemuser_it->moveNext();
				continue;
			}
			
			array_push($participants, $it->getId());
			 
			$systemuser_it->moveNext();
		}

		// process participants
		$part = $model_factory->getObject('pm_Participant');
		
		if ( count($participants) > 0 )
		{
		    $participant_it = $part->getExact($participants);
		}
		else
		{
		    $participant_it = $part->getEmptyIterator();
		}
		
		while( !$participant_it->end() )
		{
			// check is active  
			if ( $participant_it->get('IsActive') != 'Y' )
			{
				$this->info($participant_it->getDisplayName().' skipped as inactive');
				$participant_it->moveNext();
				continue;
			}
			
			// exclude a participant who initiated the notification
			if ( $participant_it->getId() == $current_part_id && $current_part_id != "" )
			{
				$this->info($participant_it->getDisplayName().' skipped as current');
				$participant_it->moveNext();
				continue;
			}

			// exclude those who don't want to receive direct notifications
			if ( !$handler->IsParticipantNotified($participant_it) )
			{
				$this->info($participant_it->getDisplayName().' skipped as non notified');
				$participant_it->moveNext();
				continue;
			}
			
			// exlude those who have no access to view object
			if ( !$handler->participantHasAccess($participant_it, $object_it) )
			{
				$this->info($participant_it->getDisplayName().' skipped as access restricted');
				$participant_it->moveNext();
				continue;
			}
			
			$caption = $this->addRecipient($participant_it, $emails);
			
			// remember the reason addressee will receive the notification
			$signature = str_replace( '%1', 
				_getServerUrl().'/pm/'.$project_it->get('CodeName').'/profile', text(1065) );
			
			if ( $this->notification_reason[$caption] == '' ) $this->notification_reason[$caption] = $signature;
			
			$participant_it->moveNext();
		}
		
		// process watchers on the object
		$watcher = $model_factory->getObject2('pm_Watcher', is_a($object_it->object, 'Comment') ? $object_it->getAnchorIt() : $object_it);

		$watcher_it = $watcher->getAll();
		
		while ( !$watcher_it->end() )
		{
			if ( $watcher_it->get('SystemUser') < 1 )
			{
				$watcher_it->moveNext();
				continue;
			}
			
			if ( $watcher_it->get('SystemUser') == $user_it->getId() )
			{
				$watcher_it->moveNext();
				continue;
			}
			
			$systemuser_it = $watcher_it->getRef('SystemUser');
			
			$address = $this->addRecipient($systemuser_it, $emails);
			
			$this->notification_reason[$address] = text(1066);
			
			$watcher_it->moveNext();
		}
		
		return $emails;
	}
		
	protected function getSubject( $object_it, $prev_object_it, $action, $recipient )
	{
		return $this->getHandler($object_it)->getSubject( $subject, $object_it, $prev_object_it, $action, $recipient );
	}

 	protected function quoteEmail( $email )
 	{
 		if ( strpos($email,",") !== false ) {
 			$email = '"'.trim($email, '"').'"'; 
 		}
 		return $email;
 	}
 	
 	protected function info( $message )
 	{
 		try {
 			Logger::getLogger('Commands')->info("EmailNotificator: ".$message);
 		}
 		catch( Exception $e )
 		{
 		}
 	}
}