<?php

define ('ROLE_PARTICIPANT', 2);

include 'UserIterator.php';
include "predicates/UserRolePredicate.php";
include "predicates/UserSessionPredicate.php";
include "predicates/UserIssuesAuthorPredicate.php";
include "predicates/UserStatePredicate.php";
include "predicates/UserSystemRolePredicate.php";
include "persisters/UserDetailsPersister.php";

class User extends Metaobject
{
 	function User( $registry = null ) 
 	{
		parent::Metaobject('cms_User', $registry);
		
		$this->setSortDefault( new SortAttributeClause('Caption') );
		
		$system_attributes = array ('IsAdmin', 'IsShared', 'Rating', 'IsActivated', 'SessionHash', 'ICQ', 'Skype', 'LDAPUID');     
		        
		foreach( $system_attributes as $attribute )
		{
    		$this->addAttributeGroup($attribute, 'system');
		}
		
		foreach( array( 'ICQ', 'Skype' ) as $attribute )
		{
		    $this->setAttributeVisible( $attribute, false );
		}
		
		$this->setAttributeCaption( 'Phone', translate('Контакты') );
		
		$this->setAttributeVisible( 'Phone', true );
		
		$this->setAttributeRequired( 'Language', false );

		$this->setAttributeType( 'Phone', 'RICHTEXT' );
		
		$this->setAttributeOrderNum('Photo', 1);
		$this->addAttributeGroup('Email', 'alternative-key');
 	}

 	function createIterator() 
	{
		return new UserIterator( $this );
	}
	
	function getPage() 
	{
		return getSession()->getApplicationUrl().'users.php?';
	}
	
 	function getDefaultAttributeValue( $name )
	{
		global $model_factory;
		
		switch ( $name )
		{
			case 'Language':

			    $settings_it = $model_factory->getObject('cms_SystemSettings')->getAll();
				
				return $settings_it->get('Language');
				
			default:
				return parent::getDefaultAttributeValue( $name );
		}
	}

	function getActiveUsers()
	{
	    global $model_factory;
	    
	    $active = $model_factory->getObject('User');
	    
	    $active->addFilter( new UserStatePredicate('active') );
	    
	    $it = $active->getCount();
	    
	    return $it->get('Count');
	}
	
 	function add_parms( $parms )
 	{
 		global $factory, $model_factory, $_REQUEST;
		
		$settings = $model_factory->getObject('cms_SystemSettings');
 		$settings_it = $settings->getAll();

		$factory = $model_factory;

		$_REQUEST['PasswordOriginal'] = $parms['Password'];
 		
 		if ( array_key_exists('Password', $parms) )
 		{
 			$parms['Password'] = $this->getHashedPassword($parms['Password']);
 		}

 	 	if ( array_key_exists('PasswordHash', $parms) )
 		{
 			$parms['Password'] = $parms['PasswordHash'];
 		}
 		
 		$parms['IsActivated'] = 'Y';
 		
 		$user_id = parent::add_parms( $parms );
 		$user_it = $this->getExact( $user_id );
 		
		// create default user role in the community
		$role = $factory->getObject('co_UserRole');

	 	$role->add_parms(
	 		array( 'SystemUser' => $user_id,
	 			   'CommunityRole' => 1 ) );
		
		// subscribe new user to the global notification via email
		//
 		$notification = $factory->getObject('cms_EmailNotification');
 		$notification_it = $notification->
 			getByRef('CodeName', "GlobalNotification");

 		$subscription = $factory->getObject('cms_NotificationSubscription');
	 	
	 	$subscription->add_parms(
	 		array( 'Notification' => $notification_it->getId(),
	 			   'Caption' => $_REQUEST['Email'],
	 			   'IsActive' => "Y" ) );
		
		// subscribe new user to vacancies notification via email
		//
 		$notification_it = $notification->
 			getByRef('CodeName', "VacancyNotification");

 		$subscription = $factory->getObject('cms_NotificationSubscription');
	 	
	 	$subscription->add_parms(
	 		array( 'Notification' => $notification_it->getId(),
	 			   'Caption' => $_REQUEST['Email'],
	 			   'IsActive' => "Y" ) );

		// send author of invitation confirmation about the user's creation
		//
 		$invitation = $factory->getObject('pm_Invitation');
 		$invitation_it = $invitation->getByRef('Addressee', $parms['Email']);
		
		if ( $invitation_it->count() > 0 )
		{
			$body = str_replace('%1', $parms['Email'], text(239)).Chr(10);
			
	   		$mail = new HtmlMailBox;
	   		
	   		$author_it = $invitation_it->getRef('Author');
	   		$mail->appendAddress($author_it->get('Email'));
	   		$mail->setBody($body);
	   		$mail->setSubject( text(238) );
	   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
			$mail->send();
		}
		
		return $user_id;
 	}

	function modify_parms( $object_id, $parms )
	{
		// store old email
		$user_it = $this->getExact($object_id);
		
		$old_email = $user_it->get('Email');

		if ( $parms['LDAPUID'] != '' ) $parms['Password'] = '';
		
 		if ( array_key_exists('Password', $parms) )
 		{
 			if ( $parms['Password'] == SHADOW_PASS )
 			{
 				unset($parms['Password']);
 			}
 			elseif ( $parms['Password'] != '' )
 			{
 				$parms['Password'] = $this->getHashedPassword($parms['Password']);
 			}
 		}

		$result = parent::modify_parms( $object_id, $parms );
		if ( $result < 1 ) return $result;
		
		$user_it = $this->getExact($object_id);
		
		// update subscriptions of the user
		if ( $old_email != $user_it->get('Email') )
		{
	 		$subscription = getFactory()->getObject('cms_NotificationSubscription');
	 		
		 	$subscription_it = $subscription->getByRef('Caption', $old_email);
		 	
		 	while ( !$subscription_it->end() )
		 	{
			 	$subscription->modify_parms(
			 		$subscription_it->getId(),
			 		array( 'Caption' => $user_it->get('Email') ) );
		 		
		 		$subscription_it->moveNext();
		 	}
		}
		
		/*
		// update participants attributes of the modified user 
		// if participant didn't override user's attributes
		$c_part = new Metaobject('pm_Participant');
		
		$c_part->setNotificationEnabled(false);
		
		$part_it = $c_part->getByRefArray(array('SystemUser' => $object_id));
		
		$attributes = array(
			'Caption' => $user_it->get('Caption'),
			'Email' => $user_it->get('Email'),
			'HomePhone' => $user_it->get('Phone'),
			'ICQNumber' => $user_it->get('ICQ'),
			'Skype' => $user_it->get('Skype')
			);
		
		for($i = 0; $i < $part_it->count(); $i++) 
		{
			$c_part->modify_parms($part_it->getId(), $attributes );

			$part_it->moveNext();
		}
		*/
		
		return $result;
	}

	/*
	 * returns latest visits of system users on the given project,
	 * also it calculates count of visits 
	 */
	function getLastVisitsOnProject( $project_id )
	{
		$sql = 
			" SELECT u.*, MAX(p.RecordModified) LastVisit, " .
			"        COUNT(p.pm_ProjectUseId) VisitsAmount " .
			"   FROM cms_User u" .
			"		 INNER JOIN pm_ProjectUse p ON p.Participant = u.cms_UserId " .
			"  WHERE p.Project = " .$project_id.
			"  GROUP BY u.cms_UserId " .
			"  ORDER BY LastVisit DESC ";
			
		return $this->createSQLIterator($sql);
	}
	
	function getActiveIt( $days )
	{
		$sql = "select p.*, max(u.RecordModified) LastAccessed " .
			   "  from cms_User p inner join pm_ProjectUse u on u.Participant = p.cms_UserId" .
			   " where to_days(now()) - to_days(u.RecordModified) < ".$days.
			   " group by p.cms_UserId " .
			   " order by LastAccessed DESC ";

		return $this->createSqlIterator($sql);
	}
	
	function getOpenProjectIt( $user_it )
	{
		$users = $user_it->idsToArray();
		
		if ( count($users) < 1 )
		{
			return null;
		}
		
		$sql = "select DISTINCT p.* " .
			   "  from pm_Project p INNER JOIN pm_Participant a on a.Project = p.pm_ProjectId" .
			   " where a.SystemUser IN (".join(',', $users).")".
			   "   and IFNULL(p.IsClosed, 'N') = 'N' ".
			   "   and IFNULL(a.IsActive, 'Y') = 'Y' ".
			   " order by p.RecordModified DESC ";

		return getFactory()->getObject('pm_Project')->createSqlIterator($sql);
	}

	function getPublicProjectIt( $user_it )
	{
		$users = $user_it->idsToArray();
		
		if ( count($users) < 1 )
		{
			return null;
		}
		
		$sql = "select p.* " .
			   "  from pm_Project p, pm_PublicInfo i " .
			   " where exists (select 1 from pm_Participant a " .
			   "			    where a.SystemUser IN (".join(',', $users).") " .
			   "				  and a.Project = p.pm_ProjectId )".
			   "   and p.pm_ProjectId = i.Project" .
			   "   and i.IsProjectInfo = 'Y' ".
			   " order by p.RecordCreated DESC ";

		return getFactory()->getObject('pm_Project')->createSqlIterator($sql);
	}

	function getSubscribedProjectIt( $user_it )
	{
		$users = $user_it->idsToArray();
		
		if ( count($users) < 1 )
		{
			return null;
		}
		
		$sql = "select p.* " .
			   "  from pm_Project p, pm_PublicInfo i " .
			   " where exists (select 1 from co_ProjectSubscription a " .
			   "			    where a.SystemUser IN (".join(',', $users).") " .
			   "				  and a.Project = p.pm_ProjectId )".
			   "   and p.pm_ProjectId = i.Project" .
			   "   and i.IsProjectInfo = 'Y' ".
			   " order by p.RecordCreated DESC ";

		return getFactory()->getObject('pm_Project')->createSqlIterator($sql);
	}

	function getCloseProjectIt( $user_it )
	{
		$users = $user_it->idsToArray();
		
		if ( count($users) < 1 )
		{
			return null;
		}
		
		$sql = "select DISTINCT p.* " .
			   "  from pm_Project p INNER JOIN pm_Participant a on a.Project = p.pm_ProjectId" .
			   " where a.SystemUser IN (".join(',', $users).")".
			   "   and IFNULL(p.IsClosed, 'N') = 'Y' ".
			   " order by p.RecordModified DESC ";

		return getFactory()->getObject('pm_Project')->createSqlIterator($sql);
	}

	function getHashedPassword( $password )
	{
		return md5(strtolower($password).PASS_KEY);
	}
	
	function getAnonymousIt()
	{
		return $this->getByRef('Login', 'anonymous');
	}

	function getAdministratorIt()
	{
		return $this->getByRef('IsAdmin', 'Y');
	}
	
	function getUsedInProjectsIt()
	{
		$sql = " SELECT u.* " .
			   "   FROM cms_User u" .
			   "  WHERE EXISTS (SELECT 1 FROM pm_Participant p, pm_ParticipantRole r" .
			   "			     WHERE p.SystemUser = u.cms_UserId" .
			   "				   AND p.IsActive = 'Y'" .
			   "				   AND r.Participant = p.pm_ParticipantId" .
			   "				   AND r.Capacity > 0 )" .
			   " ORDER BY u.Caption ASC ";
			   
		return $this->createSQLIterator( $sql );
	}
	
	function DeletesCascade( $object )
	{
		switch($object->getEntityRefName()) {
			case 'pm_Task':
			case 'pm_ChangeRequest':
			case 'pm_Question':
				return false;
		}
		return true;
	}
}
