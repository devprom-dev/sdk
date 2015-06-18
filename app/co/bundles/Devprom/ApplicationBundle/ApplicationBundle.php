<?php

namespace Devprom\ApplicationBundle;

use Devprom\Component\HttpKernel\Bundle\DevpromBundle;

include_once SERVER_ROOT_PATH."co/classes/COSession.php";
include_once SERVER_ROOT_PATH.'core/methods/WebMethod.php';
include_once SERVER_ROOT_PATH.'core/methods/SettingsWebMethod.php';
include_once SERVER_ROOT_PATH.'/core/c_command.php';

class ApplicationBundle extends DevpromBundle
{
	protected function buildSession()
	{
		$session = new \COSession(null, null, null, $this->getCacheService());

 		getFactory()->setAccessPolicy(null);
 		
 		$cache_service = getCacheService();
 		$cache_service->setDefaultPath('usr-'.$session->getUserIt()->getId());
 		
 		// define access policy
 		getFactory()->setAccessPolicy( new \CoAccessPolicy($cache_service) );
		
		return $session;
	}
	
	public function boot()
    {
    	parent::boot();

    	if ( $this->handleCustomMethod() ) die();
    	if ( $this->handleCustomCommand() ) die();
    }
    
    protected function handleCustomMethod()
    {
        if ( $_REQUEST['method'] == '' ) return false;
        if ( !class_exists($_REQUEST['method']) ) return false;
        
    	$method = new $_REQUEST['method'];
		$method->exportHeaders();
        $method->execute_request();
        
        return true;
    }
    
    protected function handleCustomCommand()
    {
    	global $plugins;
    	
	    $class = $_REQUEST['class'];

		if ( preg_match('/^[a-zA-Z0-9]+$/im', $class) < 1 ) unset($class);
		if ( !isset($class) ) return false;
		
		$module = SERVER_ROOT_PATH.'co/commands/c_'.$class.'.php';
		if ( file_exists($module) ) include( $module );
		
		if ( class_exists($class) )
		{
		 	$command = new $class;	
		}
		else
		{
		 	$command = $plugins->getCommand( $_REQUEST['namespace'], 'co', $class );
		}
		
		$command->execute();
		return true;
    }
}
