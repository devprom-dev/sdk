<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends BaseController
{
    public function indexAction(Request $request)
    {
    	// check if an update is installing then skip controlling of deployment state
    	if ( preg_match('/blacklist|users|backup|update|command|accountclient|plugin/i', $request->getBaseUrl()) ) return;

    	if ( !\DeploymentState::Instance()->IsReadyToBeUsed() )
    	{
            $this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    		return new RedirectResponse($this->generateUrl('_admin_install'));
    	}
    	
		if ( is_object($response = $this->checkAccess()) ) return $response;
    }
}