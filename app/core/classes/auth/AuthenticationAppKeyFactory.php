<?php

include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationAppKeyFactory extends AuthenticationFactory
{
 	function ready()
 	{
 	    return $_REQUEST['appkey'] != '';
 	}
 	
 	function tokenRequired()
 	{
 	    return false;
 	}
 	
 	function credentialsRequired()
 	{
 	    return true;
 	}
 	
    function getKey( $user_id )
	{
		return md5($user_id.EnvironmentSettings::getServerSalt());
	}
	
  	function authorize()
 	{
		$user = getFactory()->getObject('cms_User');
		
		if ( $_REQUEST['appkey'] == '' )
		{
 			return parent::authorize();
		}

		$user_it = $user->getAll();
		
		while ( !$user_it->end() )
		{
			if ( $this->getKey( $user_it->getId() ) == $_REQUEST['appkey'] )
			{
				$this->setUser( $user_it );
		
				return $user_it;
			}
			
			$user_it->moveNext();
		}
		
		$user_it = $user->createCachedIterator(array(
				array (
						'cms_UserId' => 999999999999
				)
		));
		 
		$this->setUser( $user_it );
		
		return $user_it;
 	}
}
