<?php
include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationNtlmKerberosFactory extends AuthenticationFactory
{
 	function ready() {
		return $this->getAuthToken() != ''
                    && $_SERVER['PHP_AUTH_PW'] == ''
                    && $_SERVER['AUTH_TYPE'] != 'form';
 	}
 	
 	function tokenRequired() {
 	    return false;
 	}
 	
 	function credentialsRequired() {
 	    return false;
 	}
 	
  	function authorize()
 	{
 		$user = getFactory()->getObject('cms_User');
	 	$user->addPersister( new UserDetailsPersister() );

        $user_it = $user->getByRefArray(array('LCASE(Login)' => strtolower($this->getAuthToken())));
        if ( $user_it->getId() < 1 ) {
            if ( !defined('AUTH_NTLM_CREATEVISITOR') ) {
                return parent::authorize();
            }

            $user_it = getFactory()->createEntity($user, array(
                'Caption' => $this->getAuthToken(),
                'Email' => $this->getAuthToken(),
                'Login' => $this->getAuthToken()
            ));
        }

 		$this->setUser( $user_it );
		return $user_it;
 	}

  	function getTitle() {
 		return 'Kerberos';
 	}

 	private function getAuthToken() {
 	    return $_SERVER['PHP_AUTH_USER'];
    }
} 