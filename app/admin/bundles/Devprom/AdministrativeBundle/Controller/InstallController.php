<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class InstallController extends PageController
{
    public function indexAction(Request $request)
    {
    	if ( getFactory()->getObject('DeploymentState')->IsReadyToBeUsed() ) {
    		return new RedirectResponse('/admin/users.php');
    	}

    	if ( getFactory()->getObject('User')->getRegistry()->Count() > 0 ) {
    		$response = $this->checkUserAuthorized($request);
    		if ( is_object($response) ) return $response;
    	}

    	include SERVER_ROOT_PATH.'admin/views/install/InstallPage.php';
    	return $this->responsePage(new \InstallPage);
    }
}