<?php
 
include_once SERVER_ROOT_PATH.'core/c_session.php';
include_once 'COAccessPolicy.php';
include_once 'COSystemTriggers.php';
 
///////////////////////////////////////////////////////////////////////
class COSession extends SessionBase
{
 	public function configure()
 	{
 		// define access policy
 		getFactory()->setAccessPolicy( new CoAccessPolicy(getFactory()->getCacheService()) );

 		// register business and aspects triggers 
 		getFactory()->getEventsManager()->registerNotificator( new COSystemTriggers );
 		
        parent::configure();
        
        getLanguage();
 	}
 	
 	function getSite()
 	{
 	    return 'co';
 	}

 	function getApplicationUrl()
 	{
 	    return '/co/';
 	}
}
