<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationDemoAnyUserFactory extends AuthenticationFactory
{
 	function ready()
 	{
		return !is_array($_COOKIE['devprom']) && getSession()->getProjectIt()->get('Platform') == 'demo';
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
 		$user_it = getFactory()->getObject('cms_User')->createCachedIterator( array (
 				array (
 						'cms_UserId' => PHP_INT_MAX,
 						'Caption' => 'Пользователь'
 				)
 		));
 			
 		$this->setUser( $user_it->getId() );
 		
 		return $user_it;
 	}

 	function validateUser( $user_it )
 	{
 	    return true;
 	}
} 