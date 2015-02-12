<?php

include_once SERVER_ROOT_PATH."cms/classes/EmailNotificator.php";

include "EmailNotificatorHandler.php";
include "CommentHandler.php";
include "ChangeRequestHandler.php";
include "MeetingParticipantHandler.php";
include "QuestionHandler.php";
include "UserMailHandler.php";
include "BlogPostHandler.php";
include "WikiHandler.php";
include "TaskHandler.php";

class PMEmailNotificator extends EmailNotificator
{
 	var $handlers, $common_handler, $notification_reason;
 	
	function __construct() 
	{
		parent::__construct();
		
		$this->common_handler = new EmailNotificatorHandler;
		
		$this->handlers = array(
			'Comment' => new CommentHandler,
			'pm_ChangeRequest' => new ChangeRequestHandler,
			'pm_MeetingParticipant' => new MeetingParticipantHandler,
			'pm_Question' => new QuestionHandler,
			'pm_UserMail' => new UserMailHandler,
			'BlogPost' => new BlogPostHandler,
			'WikiPage' => new WikiHandler,
			'pm_Task' => new TaskHandler
		);
		
		$this->notification_reason = array();
	}
 	
 	function getHandler( $object_it ) 
 	{
 		$handler = null;
 		
		if(is_object($object_it->object)) 
		{
		    if ( $object_it->object->getClassName() == 'WikiPage' )
		    {
		        if ( !is_a( $object_it->object, 'WikiPage' ) ) return $this->common_handler; 
		    }
		    
			$handler = $this->handlers[$object_it->object->getClassName()];
		}
		
		return is_object($handler) ? $handler : $this->common_handler;
 	}
 	
	function process( $action, $object_it, $prev_object_it ) 
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
			    parent::process( $action, $object_it, $prev_object_it );
				break;

			case 'pm_Task':
				
				if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() )
				{
					parent::process( $action, $object_it, $prev_object_it );
				}
				
				break;
				
			case 'WikiPage' :
				$type_it = getFactory()->getObject('WikiType')->getExact($object_it->get('ReferenceName'));
				
				switch ( $type_it->get('ReferenceName') )
				{
					case 'Requirements':
						if ( $action == 'modify' )
						{
							parent::process( $action, $object_it, $prev_object_it );
						}
						break;

					default:
						if ( $action != 'delete' )
						{
							parent::process( $action, $object_it, $prev_object_it );
						}
				}
				break;

			default:
				return;
		}
	}

	function getSender( $object_it, $action ) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getSender( $object_it, $action );
	}

	function getSubject( $object_it, $prev_object_it, $action, $recipient )
	{
		$subject = parent::getSubject($object_it, 
			$prev_object_it, $action, $recipient);
			
		$handler = $this->getHandler( $object_it );
		
		return $handler->getSubject( $subject, 
			$object_it, $prev_object_it, $action, $recipient );
	}

	function addRecipient( $object_it, &$emails )
	{
		$title = $object_it->get('Caption');
		if ( strpos($title, ",") !== false )
		{
			$title = '"'.trim($object_it->get('Caption'),'"').'"';
		}
		return $emails[] = $title.' <'.$object_it->get('Email').'>';
	}
	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
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
			if ( $systemuser_it->getId() == $current_user_id )
			{
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
				$participant_it->moveNext();
				continue;
			}
			
			// exclude a participant who initiated the notification
			if ( $participant_it->getId() == $current_part_id )
			{
				$participant_it->moveNext();
				continue;
			}

			// exclude those who don't want to receive direct notifications
			if ( !$handler->IsParticipantNotified($participant_it) )
			{
				$participant_it->moveNext();
				continue;
			}
			
			// exlude those who have no access to view object
			if ( !$handler->participantHasAccess($participant_it, $object_it) )
			{
				$participant_it->moveNext();
				continue;
			}
			
			$caption = $this->addRecipient($participant_it, $emails);
			
			// remember the reason addressee will receive the notification
			$signature = str_replace( '%1', 
				_getServerUrl().'/pm/'.$project_it->get('CodeName').'/profile.php', text(1065) );
			
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
	
	function getBody( $action, $object_it, $prev_object_it, $recipient )
	{
		global $session, $project_it;
		
		$handler = $this->getHandler( $object_it );
		
		$body = $handler->getBody( $action, $object_it, 
			$prev_object_it, $recipient );
		
		if( $body == '' ) 
		{
			$body = parent::getBody( $action, $object_it, 
				$prev_object_it, $recipient );
		}
		
		if ( $body != '' )
		{
			$result = $handler->getPreBody( $action, $object_it, $prev_object_it ).
				$body.$handler->getPostBody( $action, $object_it, $prev_object_it );
		}
		
		if ( $result == '' )
		{
			return '';
		}
		
		$mailbox = $this->getMailBox( $object_it );
		
		if ( strtolower(get_class($mailbox)) == 'htmlmailbox' )
		{
			$result .= '<br/><br/>'.$this->notification_reason[$recipient];
		}
		else
		{
			$result .= Chr(10).Chr(10).$this->notification_reason[$recipient];
		}
			
		return $result;
	}	

	function getValue( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'State':
				if ( method_exists($object_it, 'getStateIt') )
				{
					$state_it = $object_it->getStateIt();
					return $state_it->getDisplayName();
				}
			
			default:
				return parent::getValue( $object_it, $attr );
		}
	}
	
	function getMailBox($object_it) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getMailBox();
	}

	function isAttributeVisible( $attribute_name, $object_it, $action )
	{
		global $model_factory, $project_it;
		
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_Participant':
				
				if ( $attribute_name == 'Salary' )
				{
					return false;
				}

				return parent::isAttributeVisible( $attribute_name, $object_it, $action );
			
			case 'pm_Task':
				
				if ( $attribute_name == 'PercentComplete' )
				{
					return false;
				}				

				return parent::isAttributeVisible( $attribute_name, $object_it, $action );

			case 'WikiPage':
				
				switch ( $attribute_name )
				{
					case 'Content':
						return false;
						
					default:
						return parent::isAttributeVisible( $attribute_name, $object_it, $action );
				}

			case 'pm_Project':
				
				switch ( $attribute_name )
				{
					case 'Version':
					case 'Platform': 
					case 'Tools':
					case 'MainWikiPage':
					case 'RequirementsWikiPage':
					case 'Blog':
					case 'IsConfigurations':
					case 'Rating':
					case 'IsTender':
						return false;
						 
					default:
						return parent::isAttributeVisible( $attribute_name, $object_it, $action );
				}
				
			case 'pm_ChangeRequest':
				if ( $action == 'add' && $attribute_name == 'ExternalAuthor' && $object_it->get('ExternalAuthor') != '' )
				{
					return true;
				}
				
				return parent::isAttributeVisible( $attribute_name, $object_it, $action );

			default:	
				return parent::isAttributeVisible( $attribute_name, $object_it, $action );
		}
	}

	function isAttributeRequired( $attribute_name, $object_it, $action )
	{
		switch ( $object_it->object->getClassName() )
		{
			case 'pm_ChangeRequest':
				switch ( $attribute_name )
				{
					case 'ClosedInVersion':
					case 'Project':
					case 'Owner':
						return false;
				}				
				return parent::isAttributeRequired( $attribute_name, $object_it, $action );

			case 'pm_Task':
				switch ( $attribute_name )
				{
					case 'ChangeRequest':
					case 'LeftWork':
					case 'Assignee':
						return false;					
				}
				
			default:	
				return parent::isAttributeRequired( $attribute_name, $object_it, $action );
		}
	}
}
