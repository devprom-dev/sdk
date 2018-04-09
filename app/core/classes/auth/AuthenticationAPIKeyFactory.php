<?php
include_once SERVER_ROOT_PATH."core/classes/auth/AuthenticationFactory.php";

class AuthenticationAPIKeyFactory extends AuthenticationFactory
{
	const KEY_NAME = 'Devprom-Auth-Key';

	private $headers = array();
	private $readonly = false;
	private $writeonly = false;

 	function ready()
 	{
		if ( !function_exists('apache_request_headers') ) return false;
		$headers = $this->getHeaders();
 	    return $headers[self::KEY_NAME] != '';
 	}
 	
 	function tokenRequired() {
 	    return false;
 	}
 	
 	function credentialsRequired() {
 	    return false;
 	}
 	
  	function authorize()
 	{
		$headers = $this->getHeaders();
		$user_it = getFactory()->getObject('UserActive')->getRegistry()->Query();

		while ( !$user_it->end() )
		{
		    $localIt = $user_it->copy();
			if ( $this->getReadOnlyAuthKey($localIt) == $headers[self::KEY_NAME] )
			{
				$this->readonly = true;
				$this->setUser( $localIt );
				return $localIt;
			}
			if ( $this->getWriteOnlyAuthKey($localIt) == $headers[self::KEY_NAME] )
			{
				$this->writeonly = true;
				$this->setUser( $localIt );
				return $localIt;
			}
			if ( $this->getAuthKey($localIt) == $headers[self::KEY_NAME] )
			{
				$this->setUser( $localIt );
				return $localIt;
			}
			$user_it->moveNext();
		}
		return parent::authorize();
 	}

	function readOnly()	{
		return $this->readonly;
	}

	function writeOnly() {
		return $this->writeonly;
	}

	protected function getHeaders()
	{
		if ( count($this->headers) > 0 ) return $this->headers;
        $this->headers = apache_request_headers();
        if ( $_REQUEST['auth'] != '' ) {
            $this->headers = array_merge(
                $this->headers,
                array (
                    self::KEY_NAME => $_REQUEST['auth']
                )
            );
        }
		return $this->headers;
	}

	static function getAuthKey( $user_it ) {
		return md5(INSTALLATION_UID.$user_it->getId().'HeaderAuthKey');
	}

	static function getReadOnlyAuthKey( $user_it ) {
		return md5(INSTALLATION_UID.$user_it->getId().'ReadOnlyHeaderAuthKey');
	}

	static function getWriteOnlyAuthKey( $user_it ) {
		return md5(INSTALLATION_UID.$user_it->getId().'WriteOnlyHeaderAuthKey');
	}
}
