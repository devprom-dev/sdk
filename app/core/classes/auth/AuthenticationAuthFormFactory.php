<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationAuthFormFactory extends AuthenticationFactory
{
 	function ready()
 	{
		return $_SERVER['HTTP_SESSION'] != '';
 	}
 	
 	function tokenRequired()
 	{
 	    return true;
 	}
 	
 	function credentialsRequired()
 	{
 	    return false;
 	}

  	function authorize()
 	{
		$session = array();
		parse_str($_SERVER['HTTP_SESSION'], $session);

		$user_account = array_pop(preg_split("/\\\\/", $session['realm-user']));

 		$user = getFactory()->getObject('cms_User');
	 	$user->addPersister( new UserDetailsPersister() );

		Logger::getLogger('Commands')->debug("Search for user ".$user_account);

 		$user_it = $user->getByRefArray(array('LCASE(Login)' => mb_strtolower($user_account)));
 		if ( $user_it->getId() < 1 ) return parent::authorize();

 		if ( $user_it->get('Password') != '' ) {
			Logger::getLogger('Commands')->debug("Check user's password");
            if ( $user_it->get('Password') != $user->getHashedPassword($session['realm-pw']) ) return parent::authorize();
        } else {
			$remote_user = $_SERVER['REMOTE_USER'];
			Logger::getLogger('Commands')->debug("Check DN (utf-8) for ".$remote_user);
            if ( $user_it->getHtmlDecoded('LDAPUID') != $remote_user ) {
				if ( function_exists('mb_convert_encoding') ) {
					$remote_user = mb_convert_encoding($remote_user, "utf-8", "cp1251");
				}
				elseif ( function_exists('iconv') ) {
					$remote_user = iconv("cp1251", "utf-8//IGNORE", $remote_user);
				}
				Logger::getLogger('Commands')->debug("Check DN (cp1251) for ".$remote_user);
				if ( $user_it->getHtmlDecoded('LDAPUID') != $remote_user ) return parent::authorize();
			}
        }

		Logger::getLogger('Commands')->debug("User has been authenticated successfully");
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

	function logoff()
	{
		sleep(2); // wait until session is expired
	}
}