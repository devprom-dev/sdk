<?php

namespace Devprom\ApplicationBundle;

use Devprom\Component\HttpKernel\Bundle\DevpromBundle;

include SERVER_ROOT_PATH."co/classes/COSession.php";

class ApplicationBundle extends DevpromBundle
{
	protected function buildSession()
	{
		return new \COSession(null, null, null, $this->getCacheService());		
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
