<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends BaseController
{
    public function indexAction()
    {
    	$request = $this->getRequest();

    	// check if an update is installing then skip controlling of deployment state
    	if ( preg_match('/update/i', $request->getQueryString()) ) return;

    	$state = getFactory()->getObject('DeploymentState');
    	
    	if ( !$state->IsReadyToBeUsed() )
    	{
            $this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    	    
    		return new RedirectResponse($this->generateUrl('_admin_install'));
    	}
    	
		if ( is_object($response = $this->checkAccess()) ) return $response;    	
    }
}