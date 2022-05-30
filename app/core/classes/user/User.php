<?php
include 'UserIterator.php';
include "predicates/UserRolePredicate.php";
include "predicates/UserSessionPredicate.php";
include "predicates/UserStatePredicate.php";
include "predicates/UserSystemRolePredicate.php";
include "predicates/UserAccessPredicate.php";
include "persisters/UserDetailsPersister.php";
include "persisters/UserReadonlyPersister.php";
include "sorts/UserTitleSortClause.php";
include "mappers/UserLDAPMapping.php";

class User extends Metaobject
{
 	function User( $registry = null ) 
 	{
		parent::Metaobject('cms_User', $registry);
		$this->setSortDefault( new SortAttributeClause('Caption') );
 	}

 	function createIterator() 
	{
		return new UserIterator( $this );
	}

	function getSuperUserIt() {
 	    return $this->createCachedIterator( array (
 	        array (
                'Caption' => text(3113),
                'IsReadonly' => 'N'
            )
        ));
    }

	function getPage()
	{
		return '/admin/users.php?';
	}
	
 	function getDefaultAttributeValue( $name )
	{
		switch ( $name )
		{
			case 'Language':
				return getFactory()->getObject('cms_SystemSettings')->getAll()->get('Language');
            case 'NotificationTrackingType':
                return 'personal';
            case 'NotificationEmailType':
                return 'direct';
            case 'SendDeadlinesReport':
                return 'Y';
            case 'IsReadonly':
                return 'Y';
			default:
				return parent::getDefaultAttributeValue( $name );
		}
	}

 	function add_parms( $parms )
 	{
		$_REQUEST['PasswordOriginal'] = $parms['Password'];
 		
 		if ( array_key_exists('Password', $parms) ) {
 			$parms['Password'] = $this->getHashedPassword($parms['Password']);
 		}

 	 	if ( array_key_exists('PasswordHash', $parms) ) {
 			$parms['Password'] = $parms['PasswordHash'];
 		}

 		foreach( array('NotificationTrackingType', 'NotificationEmailType', 'SendDeadlinesReport') as $attribute ) {
            if ( !array_key_exists($attribute, $parms) ) {
                $parms[$attribute] = $this->getDefaultAttributeValue($attribute);
            }
        }

 		$parms['IsActivated'] = 'Y';
 		
 		$user_id = parent::add_parms( $parms );

		// send author of invitation confirmation about the user's creation
 		$invitation_it = getFactory()->getObject('pm_Invitation')->getByRef('Addressee', $parms['Email']);
		
		if ( $invitation_it->count() > 0 )
		{
	   		$mail = new HtmlMailBox;
	   		$author_it = $invitation_it->getRef('Author');
	   		$mail->appendAddress($author_it->get('Email'));
	   		$mail->setBody(str_replace('%1', $parms['Email'], text(239)).Chr(10));
	   		$mail->setSubject( text(238) );
			$mail->send();
		}
		
		return $user_id;
 	}

	function modify_parms( $object_id, $parms )
	{
		// store old email
		$user_it = $this->getExact($object_id);
		
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
		
		$participantsParms = array();
		foreach( array('NotificationTrackingType', 'NotificationEmailType') as $attribute ) {
            if ( array_key_exists($attribute,$parms) && $user_it->get($attribute) != $parms[$attribute] ) {
                $participantsParms[$attribute] = $parms[$attribute];
            }
        }
        foreach( $participantsParms as $key => $value ) {
            DAL::Instance()->Query(
                " UPDATE pm_Participant SET ".$key." = '".$value."' 
                   WHERE SystemUser = " . $user_it->getId(). "
                     AND ".$key." = '".$user_it->get($key)."' "
            );
        }

		return $result;
	}

	function getHashedPassword( $password ) {
		return md5(strtolower($password).PASS_KEY);
	}
	
	function DeletesCascade( $object ) {
 	    if ( $object->getEntityRefName() == 'co_UserGroupLink' ) return true;
		return false;
	}

	function getMappers()
    {
        return array_merge( parent::getMappers(),
            array(
                new UserLDAPMapping()
            )
        );
    }
}
