<?php
include_once "AuthenticationAppKeyFactory.php";
include_once "AuthenticationCookiesFactory.php";
include_once "AuthenticationHttpBasicFactory.php";
include_once "AuthenticationAPIKeyFactory.php";
include_once 'AuthenticationLDAPFactory.php';
include_once 'AuthenticationLDAPMixedFactory.php';
include_once 'AuthenticationAuthFormFactory.php';

class AuthenticationFactorySet
{
    private $session;
    
 	function __construct( SessionBase $session )
 	{
 	    $this->session = $session;
 	}

 	function getDefaultFactory()
 	{
 	    return new AuthenticationCookiesFactory();
 	}
 	
 	function getFactories()
 	{
 	    $result = array(
            new AuthenticationAppKeyFactory(),
            new AuthenticationAPIKeyFactory(),
            new AuthenticationAuthFormFactory(),
            new AuthenticationLDAPFactory(),
            new AuthenticationLDAPMixedFactory()
        );

        $plugins = getFactory()->getPluginsManager();
        if ( is_object($plugins) ) {
		    foreach( $plugins->getAuthFactories() as $factory ) {
		        $result[] = $factory;
		    }
        }
 		
        $result[] = new AuthenticationHttpBasicFactory();
        $result[] = $this->getDefaultFactory();
        
        return $result;
 	}
}