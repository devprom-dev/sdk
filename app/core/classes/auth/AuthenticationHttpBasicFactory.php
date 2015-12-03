<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationHttpBasicFactory extends AuthenticationFactory
{
 	function ready()
 	{
		return $_SERVER['PHP_AUTH_USER'] != '' && $_SERVER['PHP_AUTH_PW'] != '';
 	}
    
 	function tokenRequired()
 	{
 	    return false;
 	}
 	
 	function credentialsRequired()
 	{
 	    return true;
 	}
 	
 	function logon( $remember = false )
 	{
	    header('WWW-Authenticate: Basic realm="Devprom"');
	    
        exit(header('HTTP/1.0 401 Unauthorized'));
        
        return parent::authorize();
 	}
 	
    function authorize()
 	{
		global $_SERVER, $model_factory;
		
		$user = $model_factory->getObject('cms_User');
		
	 	$user->addPersister( new UserDetailsPersister() );
		
		if ( !DeploymentState::IsInstalled() )
		{
			return $user->createCachedIterator( array( 
				array('IsAdministrator' => 'Y') 
			));
		}

 		$user_it = $user->getByRef('LCASE(Login)', strtolower($_SERVER['PHP_AUTH_USER']));
 		
 		if ( $user_it->get('Password') != $user->getHashedPassword($_SERVER['PHP_AUTH_PW']) ) return parent::authorize();
 		
 		$this->setUser( $user_it->getId() );
 		
		return $user_it;
 	}

	function validateUser( $user_it )
	{
		return $user_it->get('Password') != '';
	}
}