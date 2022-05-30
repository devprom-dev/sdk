<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationCookiesFactory extends AuthenticationFactory
{
 	function ready()
 	{
 	    return is_array($_COOKIE['devprom'])
			&& $_SERVER['AUTH_TYPE'] == ''
			&& $_SERVER['no-auth'] == ''
			&& $_SERVER['REDIRECT_no-auth'] == '';
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
 		return md5(SystemDateTime::date("d.m.Y. h:i:s").PASS_KEY.$this->getUser()->getId().INSTALLATION_UID);
 	}
 	
 	function logon( $remember = false, $session_hash = '' )
 	{
 		global $_SERVER, $_COOKIE;
 		
		$cookie_session = $this->getToken();
 		$cookie_expires = $remember ? mktime(0, 0, 0, 1, 1, date('Y') + 1) : 0;

 		$session_hash = $this->getSessionId();

		setcookie('devprom['.$cookie_session.']', $session_hash, 
			$cookie_expires, '/', $_SERVER['HTTP_HOST'], false, true  );

		setcookie('devprom['.$cookie_session.']', $session_hash, 
			$cookie_expires, '/', '', false, true );

        parent::logon($remember, $session_hash);
 	}
 	
 	function logoff()
 	{
		$tokens = array( $this->getToken() );

		if ( is_array($_COOKIE['devprom']) ) {
			$tokens = array_merge($tokens, array_keys($_COOKIE['devprom']));
		}
		
		foreach( $tokens as $cookie_session ) {
			setcookie('devprom['.$cookie_session.']', '', 0, '/', $_SERVER['HTTP_HOST'], false, true );
			setcookie('devprom['.$cookie_session.']', '', 0, '/', '', false, true );
		}
		
		setcookie('devprom', '', 0, '/', $_SERVER['HTTP_HOST'], false, true );
		setcookie('devprom', '', 0, '/', '', false, true );
		
		return true;
 	}

  	function authorize()
 	{
		if ( is_object($this->getUser()) ) return $this->getUser();

	 	$user = getFactory()->getObject('cms_User');
		$cookies = is_array($_COOKIE['devprom']) ? array_values($_COOKIE['devprom']) : array('*');

		$user->addPersister( new UserDetailsPersister() );
		$user->addFilter( new UserSessionPredicate($cookies) );

 		$user_it = $user->getAll();
 		if ( $user_it->count() < 1 ) return parent::authorize();
 		
 		$this->setUser( $user_it );
		return $user_it;
 	}
 	
 	function getTitle()
 	{
 		return text(1041);
 	}

	function validateUser( $user_it )
	{
		return $user_it->get('Password') != '';
	}
}