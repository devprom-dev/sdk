<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationLDAPFactory extends AuthenticationFactory
{
 	function ready()
 	{
		return $_SERVER['PHP_AUTH_USER'] != ''
            && $_SERVER['HTTP_SESSION'] == ''
            && $_SERVER['PHP_AUTH_MODE'] == ''
            && $_SERVER['AUTH_TYPE'] != 'form';
 	}
 	
 	function tokenRequired()
 	{
 	    return false;
 	}
 	
 	function credentialsRequired()
 	{
 	    return false;
 	}
 	
  	function authorize()
 	{
		$user_account = $_SERVER['PHP_AUTH_USER'];

		$parts = preg_split("/\\\\/", $user_account);
		if( count($parts) > 1 ) $user_account = $parts[1];
		
 		$user = getFactory()->getObject('cms_User');
	 	$user->addPersister( new UserDetailsPersister() );

 		$user_it = $user->getByRefArray(array('LCASE(Login)' => strtolower($user_account)));
 		if ( $user_it->getId() < 1 ) {
			$user_it = $user->getByRefArray(array('LCASE(LDAPUID)' => strtolower($user_account)));
			if ( $user_it->getId() < 1 ) {
				return parent::authorize();
			}
		}
 		if ( $user_it->get('Password') != '' && $user_it->get('Password') != $user->getHashedPassword($_SERVER['PHP_AUTH_PW']) ) {
 		    return parent::authorize();
 		}
        if ( !$this->validateUser($user_it) ) return parent::authorize();

 		$this->setUser( $user_it );
		return $user_it;
 	}

 	function validateUser( $user_it )
 	{
 	    return $user_it->get('LDAPUID') != '';
 	}
 	
  	function getTitle()
 	{
 		return text(2787);
 	}
} 