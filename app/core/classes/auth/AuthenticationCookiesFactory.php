<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationCookiesFactory extends AuthenticationFactory
{
 	function ready()
 	{
 	    return is_array($_COOKIE['devprom']);
 	}
 	
 	function tokenRequired()
 	{
 	    return true;
 	}
    
 	function credentialsRequired()
 	{
 		return true;
 	}
 
 	function getSessionId()
 	{
 		return md5(SystemDateTime::date("d.m.Y. h:i:s").$this->getUser().INSTALLATION_UID);
 	}
 	
 	function logon( $remember = false )
 	{
 		global $_SERVER, $_COOKIE;
 		
		$cookie_session = $this->getToken();
 		$cookie_expires = $remember ? mktime(0, 0, 0, 1, 1, date('Y') + 1) : 0;

 		$session_hash = $this->getSessionId();

		setcookie('devprom['.$cookie_session.']', $session_hash, 
			$cookie_expires, '/', $_SERVER['HTTP_HOST'] );

		setcookie('devprom['.$cookie_session.']', $session_hash, 
			$cookie_expires, '/' );
 		
		return $session_hash;
 	}
 	
 	function logoff()
 	{
 		global $_SERVER, $_COOKIE;
 		
		$tokens = array( $this->getToken() );

		if ( is_array($_COOKIE['devprom']) )
		{
			$tokens = array_merge($tokens, array_keys($_COOKIE['devprom']));
		}
		
		foreach( $tokens as $cookie_session )
		{
			setcookie('devprom['.$cookie_session.']', '', 0, '/', $_SERVER['HTTP_HOST'] );
			
			setcookie('devprom['.$cookie_session.']', '', 0, '/' );
		}
		
		setcookie('devprom', '', 0, '/', $_SERVER['HTTP_HOST'] );
		
		setcookie('devprom', '', 0, '/' );
		
		return true;
 	}

  	function authorize()
 	{
		global $_COOKIE, $model_factory, $session;
		
	 	$user = $model_factory->getObject('cms_User');
	 	
		$cookies = is_array($_COOKIE['devprom']) ? array_values($_COOKIE['devprom']) : array('*');
		
		foreach( $cookies as $cookie )
		{
	 		$data = $session->get('session-'.$cookie, 'usr');

	 		if ( is_array($data) )
	 		{
	 		    $user_it = $user->createCachedIterator( $data );
	 		    
	 		    $this->setUser( $user_it->getId() );
	 		    
	 			return $user_it;
	 		}
		}
		
		$user->addPersister( new UserDetailsPersister() );
		
		$user->addFilter( new UserSessionPredicate($cookies) );

 		$user_it = $user->getAll();

 		if ( $user_it->count() < 1 ) return parent::authorize();
 		
 		$this->setUser( $user_it->getId() );

 		foreach( $cookies as $cookie )
		{
 			$session->set('session-'.$cookie, $user_it->getRowset(), 'usr');
		}
 		
		return $user_it;
 	}
 	
 	function getTitle()
 	{
 		return text(1041);
 	}
}