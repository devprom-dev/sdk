<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/ProfilePage.php";
 
class ProfileController extends PageController
{
    public function formAction(Request $request)
    {
        $response = $this->checkUserAuthorized($request);
        
        if ( is_object($response) ) return $response;
        
    	return $this->responsePage( new \ProfilePage() );
    }
}