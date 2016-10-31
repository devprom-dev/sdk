<?php

class AuthenticationFactory
{
 	private $user = null;
 	
 	function __construct( UserIterator $user_it = null )
 	{
 		$this->setUser($user_it);
 	}
 	
 	function ready()
 	{
 	    return false;
 	}

 	function __sleep() {
        return array();
    }

    function __wakeup() {
        $this->setUser(null);
    }

    // to use authentication password is required to be stored in the database
 	function credentialsRequired()
 	{
 	    return true;
 	}
 	
 	// token is used to authenticate user, thus logoff make sense
 	function tokenRequired()
 	{
 	    return true;
 	}

 	function authorize()
 	{
 		if ( is_object($this->getUser()) ) return $this->getUser();
 		
		$user = new User();
		return $user->createCachedIterator( array(
			array(
				'IsAdministrator' => $user->getRegistry()->Count(array(new FilterAttributePredicate('IsAdmin', 'Y'))) > 0
					? 'N' : 'Y')
			)
		);
 	}
 	
 	function logoff()
 	{
        unset($this->user);

        return false;
 	}
 	
 	function logon( $remember = false )
 	{
 		return false;
 	}
 	
 	function getToken()
 	{
 		return md5($this->getUser()->getId().EnvironmentSettings::getServerSalt());
 	}

 	function setUser( $user )
 	{
 		$this->user = $user;
 	}
 	
 	function getUser()
 	{
 		return $this->user;
 	}

 	function validateUser( $user_it )
 	{
 		return false;
 	}
 	
 	function getTitle()
 	{
 		return '';
 	}

    function readOnly()
    {
        return false;
    }

    function writeOnly()
    {
        return false;
    }
}