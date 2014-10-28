<?php

class EmailNotificatorHandler
{
 	function getSender( $object_it, $action ) 
 	{
		$part_it = getSession()->getParticipantIt();
		
		$user_it = getSession()->getUserIt();
		
		if ( is_object($part_it) && $part_it->get('Email') != '' )
		{
			return '"'.trim($part_it->get('Caption'), '"').'" <'.$part_it->get('Email').'>';
		}
		else
		{
			if ( $user_it->count() < 1 )
			{
		 		return getFactory()->getObject('cms_SystemSettings')->getAll()->getHtmlDecoded('AdminEmail');
			}
			else
			{
				return '"'.trim($user_it->get('Caption'), '"').'" <'.$user_it->get('Email').'>';
			}
		}
 	}
 	
	function getSubject( $subject, $object_it, $prev_object_it, $action, $recipient )
	{
		global $model_factory, $project_it;
		
		return '['.$project_it->get('CodeName').'] '.$subject;
	}
	
	function getParticipants( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	
	
	function getUsers( $object_it, $prev_object_it, $action ) 
	{
		return array();
	}	
	
	function getPreBody( $action, $object_it, $prev_object_it ) 
	{
		// draw object uuid url
		if($action != 'delete') 
		{
			$object_uid_url = $this->getObjectItUid( $object_it );
		}
		
		if ( strtolower(get_class($this->getMailBox())) == 'htmlmailbox' )
		{
			$object_uid_url = $object_it->getDisplayName().'<br/><a href="'.$object_uid_url.'">'.$object_uid_url.'</a>';

			$line_delimiter = '<br/>';
			
			if ( $action == 'modify' )
			{
				$object_uid_url .= '<br/>'.translate('Автор изменения').': '.getSession()->getUserIt()->getDisplayName();
			}
		}
		else
		{
			$line_delimiter = chr(10);
		}
		
		
		return $object_it->object->getDisplayName().
			': '.$object_uid_url.$line_delimiter.$line_delimiter;
	}	

	function getObjectItUid( $object_it ) 
	{
		global $session, $project_it;
		
		$uid = new ObjectUid;
		
		if ( $uid->HasUid( $object_it) )
		{
		    $info = $uid->getUIDInfo( $object_it );
		    
			return $info['url'];
		}
	}

	function getBody( $action, $object_it, $prev_object_it, $recipient ) 
	{
		return '';
	}	

	function getPostBody( $action, $object_it, $prev_object_it ) 
	{
		return '';
	}	
	
	function getMailBox() 
	{
		return new HtmlMailBox;
	}
	
	function IsParticipantNotified( $participant_it )
	{
		global $model_factory;
		
		$notification = $model_factory->getObject('Notification');
		$notification_type = $notification->getType( $participant_it );
							
		return $notification_type == 'all' || $notification_type == 'system';
	}
	
	function participantHasAccess( $participant_it, $object_it )
	{
		if ( $object_it->getId() == '' ) return false;
		
		$policy = getFactory()->getAccessPolicy();
		
		if ( $policy instanceof AccessPolicyBase )
		{
			$roles = $participant_it->getRoles();
			 
		    foreach( $roles as $key => $role )
	        {
	            if ( $role < 1 ) $roles[$key] = $policy->getRoleByBase( $role );
	        }
			
	        $class_name = get_class($policy);
	        
	        $policy = new $class_name(getFactory()->getCacheService());
	        
	        $policy->setRoles($roles);
		}
		
		return $policy->getObjectAccess(ACCESS_READ, $object_it) && $policy->getEntityAccess(ACCESS_READ, $object_it->object);
	}
}
