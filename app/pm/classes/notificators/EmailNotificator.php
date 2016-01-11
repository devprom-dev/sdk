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
		$this->process( 'add', $object_it, $object_it->object->getEmptyIterator() );
	}

 	function modify( $prev_object_it, $object_it ) 
	{
		$this->process( 'modify', $object_it, $prev_object_it );
	}

 	function delete( $object_it ) 
	{
		$this->process( 'delete', $object_it->object->getEmptyIterator(), $object_it );
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
		
		$from = $this->getSender($object_it, $action);
		if( $from == '' ) {
			$this->info('Sender is undefined');
			return $queues;
		}

		$self_emails = array();

		// skip recipients who are support mailboxes to avoid notification cycles
		$mailbox_it = getFactory()->getObject('co_RemoteMailbox')->getRegistry()->Query();
		$self_emails = array_merge( $self_emails,
			array_filter($mailbox_it->fieldToArray('EmailAddress'),
				function($email) {
					return $email != '';
				}
			)
		);
		$self_emails = array_merge( $self_emails,
			array_filter($mailbox_it->fieldToArray('SenderAddress'),
				function($email) {
					return $email != '';
				}
			)
		);
		// skip current user
		$this->addRecipient(getSession()->getUserIt(), $self_emails);

		$recipients = array_diff(
				array_unique($this->getRecipientArray($object_it, $prev_object_it, $action)),
				$self_emails
		);
		if( count($recipients) < 1 ) {
			$this->info('There are no recipients');
			return $queues;
		}

		$render_service = new RenderService(
				getSession(), SERVER_ROOT_PATH."pm/bundles/Devprom/ProjectBundle/Resources/views/Emails"
		);

		$keys = array_keys($recipients);
		for($i = 0; $i < count($keys); $i++) 
		{
			$recipient = $recipients[$keys[$i]];

			$parms = $this->getRenderParms($action, $object_it, $prev_object_it, $recipient);
			if ( count($parms['fields']) < 1 ) continue;

			$mail = new HtmlMailBox();
			$mail->setFrom($from);
			$mail->appendAddress( $this->getAddress($recipient) );
			$mail->setSubject( $this->getSubject( $object_it, $prev_object_it, $action, $recipient ) );
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
		$user_it = getSession()->getUserIt();
		if ( $user_it->get('Email') != '' ) {
			return $this->quoteEmail($user_it->get('Caption')).' <'.$user_it->get('Email').'>';
		}
		else {
			return getFactory()->getObject('cms_SystemSettings')->getAll()->getHtmlDecoded('AdminEmail');
		}
	}

	protected function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		$project_it = getSession()->getProjectIt();
		$notification = getFactory()->getObject('Notification');
		
		$handler = $this->getHandler( $object_it );

		$participants = array_filter($handler->getParticipants( $object_it, $prev_object_it, $action ), function($id) {
			return is_numeric($id) && $id > 0;
		});

		$users = array_filter($handler->getUsers( $object_it, $prev_object_it, $action ), function( $id ) {
			return is_numeric($id) && $id > 0;
		});
		
		// include participants who wants to receive all notifications
		$participant = getFactory()->getObject('Participant');
		$participant->addFilter( new ParticipantActivePredicate() );
		$it = $participant->getAll();
		
		while ( !$it->end() ) {
			if ( $notification->getType( $it ) != 'all' ) {
			    $it->moveNext();
			    continue;
			}
			$participants[] = $it->getId();
			
			$it->moveNext();
		}

		// make email addresses
		$emails = array();

		// process users
		$user = getFactory()->getObject('cms_User');
		if ( count($users) > 0 ) {
		    $systemuser_it = $user->getRegistry()->Query(
				array (
					new UserStatePredicate('active'),
					new FilterInPredicate($users)
				)
			);
		}
		else {
		    $systemuser_it = $user->getEmptyIterator();
		}
		
		while( !$systemuser_it->end() )
		{
			// check if user is a prticipant
			$it = $project_it->getParticipantForUserIt( $systemuser_it );
			if ( $it->count() < 1 ) {
				$this->addRecipient($systemuser_it, $emails);
			}
			else {
				$participants[] = $it->getId();
			}
			$systemuser_it->moveNext();
		}

		// process participants
		if ( count($participants) > 0 ) {
		    $participant_it = $participant->getRegistry()->Query(
				array (
					new ParticipantActivePredicate(),
					new FilterInPredicate($participants)
				)
			);
		}
		else {
		    $participant_it = $participant->getEmptyIterator();
		}
		
		while( !$participant_it->end() )
		{
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
		$emails = array_merge($emails, $this->addWatchers($object_it));

		// process mentions on the content
		$emails = array_merge($emails, $this->addMentions($object_it));

		return $emails;
	}

	protected function addWatchers( $object_it )
	{
		$emails = array();

		$watcher_it = getFactory()->getObject2('pm_Watcher',
			is_a($object_it->object, 'Comment') ? $object_it->getAnchorIt() : $object_it)->getAll();

		while ( !$watcher_it->end() )
		{
			// skip current user or external emails
			if ( in_array($watcher_it->get('SystemUser'), array('', getSession()->getUserIt()->getId())) ) {
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

	protected function addMentions( $object_it )
	{
		$emails = array();
		$matches = array();

		// get any available text
		$texts = $object_it->getHtmlDecoded('Caption');
		$texts .= $object_it->getHtmlDecoded('Content');
		$texts .= $object_it->getHtmlDecoded('Description');

		if ( !preg_match_all('/@(\w*)/u', $texts, $matches) ) return $emails;
		array_shift($matches);
		$matches = array_shift($matches);

		// convert mentions into system users
		$user_ids = array();
		$mention_it = getFactory()->getObject('Mentioned')->getAll();
		while( !$mention_it->end() ) {
			if ( in_array($mention_it->getId(), $matches) ) {
				$user_ids[] = $mention_it->get('User');
			}
			$mention_it->moveNext();
		}

		// skip current user
		$user_id = getSession()->getUserIt()->getId();
		$user_ids = array_filter(
				preg_split('/,/',join(',',$user_ids)),
				function($value) use ($user_id) {
					return $value != '' && trim($value) != $user_id;
				}
		);
		if ( count($user_ids) < 1 ) return $emails;

		// convert users into emails
		$user_it = getFactory()->getObject('User')->getExact($user_ids);
		while ( !$user_it->end() )
		{
			$address = $this->addRecipient($user_it, $emails);
			$this->notification_reason[$address] = text(2103);
			$user_it->moveNext();
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