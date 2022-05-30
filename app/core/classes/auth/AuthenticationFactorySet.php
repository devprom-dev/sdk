<?php
include "AuthenticationAppKeyFactory.php";
include "AuthenticationCookiesFactory.php";
include "AuthenticationHttpBasicFactory.php";
include "AuthenticationAPIKeyFactory.php";
include 'AuthenticationAuthFormFactory.php';
include 'AuthenticationOpenIDFactory.php';
include 'AuthenticationNtlmKerberosFactory.php';

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
            new AuthenticationOpenIDFactory(),
            new AuthenticationNtlmKerberosFactory(),
            new AuthenticationAuthFormFactory()
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