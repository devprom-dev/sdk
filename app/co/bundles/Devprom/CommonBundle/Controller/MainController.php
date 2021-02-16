<?php

namespace Devprom\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends Controller
{
    protected function checkDeploymentState(Request $request)
    {
    	// check if an update is installing then skip controlling of deployment state
    	if ( preg_match('/backup|update|accountclient|plugin/i', $request->getQueryString()) ) return;

    	// check other entry points
    	if ( preg_match('/login/i', $request->getPathInfo()) && getFactory()->getObject('User')->getRegistry()->Count() > 0 ) return;
    	
    	if ( !\DeploymentState::Instance()->IsReadyToBeUsed() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    		
    		if ( getFactory()->getObject('User')->getRegistry()->Count() > 0 && !is_object($this->checkUserAuthorized($request)) )
    		{
    			return new RedirectResponse( 
 	                $this->generateUrl('login', 
 	                        array('page' => $request->server->get('REQUEST_URI'))
 	                        )
 	                );
    		}
    		else
    		{
    			return new RedirectResponse('/install');
    		}
    	}
    	
        if ( \DeploymentState::IsMaintained() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    		return new RedirectResponse('/503');
    	}
    }
    
    protected function checkUserAuthorized(Request $request)
    {
     	$user_it = getSession()->getUserIt();
     	if ( $user_it->getId() > 0 ) return;
     	
 	    $auth_factory = getSession()->getAuthenticationFactory();
 	    if ( !is_object($auth_factory) ) {
 	    	return new RedirectResponse( '/logoff?redirect='.$request->server->get('REQUEST_URI') );
 	    }
	
 	    return $auth_factory->credentialsRequired() 
 	        ? new RedirectResponse( '/logoff?redirect='.$request->server->get('REQUEST_URI') )
 	        : new RedirectResponse( '/404?redirect='.$request->server->get('REQUEST_URI') );
    }
}