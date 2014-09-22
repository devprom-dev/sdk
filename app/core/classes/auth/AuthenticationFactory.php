<?php

class AuthenticationFactory
{
 	var $user;
 	
 	function ready()
 	{
 	    return true;
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
 		global $model_factory;

 		if ( is_object($this->user) ) return $this->user;
 		
		$user = $model_factory->getObject('cms_User');
		
		$user->addFilter( new FilterAttributePredicate('IsAdmin', 'Y') );
		
		$user_it = $user->getAll(); 
		
		return $user->createCachedIterator( array( array(
			'IsAdministrator' => $user_it->count() > 0 ? 'N' : 'Y') 
		));
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
 		return md5($this->getUser().EnvironmentSettings::getServerSalt());
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
 		return true;
 	}
 	
 	function getTitle()
 	{
 		return '';
 	}
}