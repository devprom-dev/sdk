<?php
/*
 * DEVPROM (http://www.devprom.net)
 * c_email_notificator.php
 *
 * Copyright (c) 2005, 2006 Evgeny Savitsky <admin@devprom.net>
 * You can modify this code freely for your own needs,
 * but you can't distribute it manually.
 * 
 */

include_once SERVER_ROOT_PATH."cms/classes/EmailNotificator.php";

 ///////////////////////////////////////////////////////////////////////////////
 class CoEmailNotificatorHandler
 {
 	function getEmailAddress( $part_it ) 
 	{
		return $part_it->get('Email');
	}

 	function getSender( $object_it, $action ) 
 	{
 		global $model_factory;
 		
 		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

		return $settings_it->getHtmlDecoded('AdminEmail');
 	}
 	
	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		return $subject;
	}
	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	

	function getPreBody( $action, $object_it, $prev_object_it ) 
	{
		return '';
	}	

	function getBody( $action, $object_it, $prev_object_it ) 
	{
		return '';
	}	

	function getMailBox() 
	{
		return new HtmlMailBox;
	}
	
 	function getRecentComments( $object_it )
 	{
 		$comment_it = $object_it->getRollupIt(2);
		return $this->_getRecentComments( $comment_it, 0 );
 	}

 	function _getRecentComments( $comment_it, $level )
 	{
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////
 class CoMessageHandler extends CoEmailNotificatorHandler
 {
	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		if ( $action == 'add' )
		{
			return text('procloud174');
		}
		else
		{
			return text('procloud578');
		}
	}
	
	function getRecipientArray( $message_it, $comment_it, $action ) 
	{
		$addresses = array();
	
		if ( $action == 'delete' )
		{
			return $addresses;
		}
		
		if ( $message_it->get('ToUser') > 0 )
		{
			$user_it = $message_it->getRef('ToUser');
			array_push($addresses, $user_it->get('Email'));
		}
		else
		{
			$team_it = $message_it->getRef('ToTeam');
			$addresses = $team_it->getMembersEmails();
		}
		
		$author_it = $message_it->getRef('Author');
		array_push($addresses, $author_it->get('Email'));
		
		if ( $action == 'commented' )
		{
			$addresses = $comment_it->getThreadEmails($addresses);
		}
		
		return $addresses;
	}	

	function getBody( $action, $message_it, $comment_it )
	{
		if ( $action == 'add' )
		{
			if ( $message_it->get('ToUser') > 0 )
			{
				$body = nl2br(text('procloud119'));

				$user_it = $message_it->getRef('ToUser');
				$body = str_replace('%4', $user_it->getDisplayName(), $body);
			}
			else
			{
				$body = text('procloud123');

				$team_it = $message_it->getRef('ToTeam');
				$body = str_replace('%4', $team_it->getRefLink(), $body);
			}
		
			$author_it = $message_it->getRef('Author');
				
			$body = str_replace('%1', 
				'<a href="'.ParserPageUrl::parse($author_it).'">'.
					$author_it->getDisplayName().'</a>', $body);
				
			$body = str_replace('%2', 
				'<a href="'._getServerUrl().ParserPageUrl::parse($message_it).'">'.
					$message_it->get('Subject').'</a>', $body);
	
			$body = str_replace('%3', 
				$message_it->get('Content'), $body);
		}
			
		if ( $action == 'commented' )
		{
			$body = nl2br(text('procloud120'));
		
			$body = str_replace('%1', 
				'<a href="'._getServerUrl().ParserPageUrl::parse($message_it).'">'.
					$message_it->get('Subject').'</a>', $body);
	
			$body = str_replace('%2', $this->getRecentComments($comment_it), $body);
		}

		return $body;
	}	
 }
 
 ///////////////////////////////////////////////////////////////////////////////
 class CoBillOperationHandler extends CoEmailNotificatorHandler
 {
	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		return text('procloud177');
	}
	
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		$addresses = array();

		$bill_it = $object_it->getRef('Bill');
		$user_it = $bill_it->getRef('SystemUser');
			
		array_push($addresses, $user_it->get('Email'));
		
		return $addresses;
	}	
 }
   
 ///////////////////////////////////////////////////////////////////////////////
 class CoInvitationHandler extends CoEmailNotificatorHandler
 {
	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		if ( $action == 'add' )
		{
			$result = array();
			array_push($result, $object_it->get('Addressee'));
		}
		
		return $result;
	}	

	function getBody( $action, $object_it, $prev_object_it )
	{
		if ( $action == 'add' )
		{
			$user_it = $object_it->getRef('Author');
			$project_it = $object_it->getRef('Project');
			
			$body .= str_replace('%1', $user_it->getDisplayName(), text(261));
			$body = str_replace('%2', $project_it->getDisplayName(), $body);
			$body = str_replace('%3', $object_it->get('Addressee'), $body);
			
			return $body;
		} 
	}

	function getSubject( $subject, $object_it, $prev_object_it, $action )
	{
		return text(262);
	}
	
	function getPostBody( $action, $object_it, $prev_object_it )
	{
		return ''; 
	}
 }  
 
 ///////////////////////////////////////////////////////////////////////////////
 class CoEmailNotificator extends EmailNotificator
 {
 	var $handlers, $common_handler;
 	
	function CoEmailNotificator() 
	{
		parent::__construct();
		
		$this->common_handler = new CoEmailNotificatorHandler;
		
		$this->handlers = array(
				'co_BillOperation' => new CoBillOperationHandler,
				'co_Message' => new CoMessageHandler
			);
	}
 	
 	function getHandler( $object_it ) 
 	{
 		$handler = null;
		if(is_object($object_it->object->entity)) 
		{
			$handler = $this->handlers[$object_it->object->entity->get('ReferenceName')];
		}
		return is_object($handler) ? $handler : $this->common_handler;
 	}
 	
	function process( $action, $object_it, $prev_object_it ) 
	{
		if ( !is_object($object_it->object->entity) ) return;

		switch ( $object_it->object->entity->get('ReferenceName') )
		{
			case 'co_BillOperation' :
			case 'co_Message' :
			case 'pm_Question':
			case 'pm_ChangeRequest':
				
				parent::process( $action, $object_it, $prev_object_it );
				break;
			
			case 'Comment':
				$anchor_it = $object_it->getAnchorIt();

				switch ( $anchor_it->object->getClassName() )
				{
					case 'co_Message':
					case 'pm_Question':
					case 'pm_ChangeRequest':
					case 'BlogPost':
					
						parent::process( 'commented', $anchor_it, $object_it );
						break;
				}
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
		$subject = parent::getSubject($object_it, $prev_object_it, $action, $recipient);
		$handler = $this->getHandler( $object_it );
		return $handler->getSubject( $subject, $object_it, $prev_object_it, $action );
	}

	function getRecipientArray( $object_it, $prev_object_it, $action ) 
	{
		global $model_factory;
		
		$user_it = getSession()->getUserIt();
		
		$handler = $this->getHandler( $object_it );
		$recipients = $handler->getRecipientArray( $object_it, $prev_object_it, $action );

		// exclude user who initiated the notification
		$recipients = $this->exclude( $recipients, $user_it->get('Email') );

		$recipients = array_unique($recipients);
		
		return $recipients;
	}
	
	function exclude( $recipients, $email )
	{
		$keys = array_keys($recipients);
		for ( $i = 0; $i < count($keys); $i++ )
		{	
			if (strpos( $recipients[$keys[$i]], $email ) !== false )
			{
				unset($recipients[$keys[$i]]);
			}
		}
		
		return $recipients;
	}
	
	function getBody( $action, $object_it, $prev_object_it )
	{
		global $session;
		
		$handler = $this->getHandler( $object_it );
		$prebody = $handler->getPreBody( $action, $object_it, $prev_object_it );

		if ( $prebody != '' )
		{
			$body .= $prebody.Chr(10).Chr(10);
		}
		
		$handler_body = $handler->getBody( $action, $object_it, $prev_object_it );
		
		if ( $handler_body == '' )
		{
			$body .= parent::getBody( $action, $object_it, $prev_object_it );
		}
		else
		{
			$body .= $handler_body;
		}
			
		return $body;
	}	

	function getMailBox($object_it) 
	{
		$handler = $this->getHandler( $object_it );
		return $handler->getMailBox();
	}
 }
 
?>
