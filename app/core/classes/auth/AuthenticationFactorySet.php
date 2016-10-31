<?php

include_once "AuthenticationAppKeyFactory.php";
include_once "AuthenticationCookiesFactory.php";
include_once "AuthenticationHttpBasicFactory.php";
include_once "AuthenticationAPIKeyFactory.php";

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
 	    $result = array();
 	    
        $result[] = new AuthenticationAppKeyFactory();
 	    $result[] = new AuthenticationAPIKeyFactory();

        $plugins = getFactory()->getPluginsManager();
 	    
        if ( is_object($plugins) )
        {
		    foreach( $plugins->getAuthFactories() as $factory )
		    {
		        $result[] = $factory;
		    }
        }
 		
        $result[] = new AuthenticationHttpBasicFactory();
        
        $result[] = $this->getDefaultFactory();
        
        return $result;
 	}
}