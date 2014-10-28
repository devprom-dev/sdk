<?php

namespace Devprom\ApplicationBundle;

use Devprom\Component\HttpKernel\Bundle\DevpromBundle;

include SERVER_ROOT_PATH."co/classes/COSession.php";

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
         
        if ( $_REQUEST['method'] != '' && class_exists($_REQUEST['method']) )
	    {
			$method = new $_REQUEST['method'];

			$method->exportHeaders();

            $method->execute_request();

            die();
		}
    }
}
