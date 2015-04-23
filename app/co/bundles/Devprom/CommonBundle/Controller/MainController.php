<?php

namespace Devprom\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends Controller
{
    protected function checkDeploymentState()
    {
    	$request = $this->getRequest();
    	
    	// check if an update is installing then skip controlling of deployment state
    	if ( preg_match('/backup|update|accountclient/i', $request->getQueryString()) ) return;

    	// check other entry points
    	if ( preg_match('/login/i', $request->getPathInfo()) && getFactory()->getObject('User')->getRegistry()->Count() > 0 ) return;
    	
    	$state = getFactory()->getObject('DeploymentState');
    	
    	if ( !$state->IsReadyToBeUsed() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    		
    		if ( getFactory()->getObject('User')->getRegistry()->Count() > 0 && !is_object($this->checkUserAuthorized()) )
    		{
    			return new RedirectResponse( 
 	                $this->generateUrl('login', 
 	                        array('page' => $this->getRequest()->server->get('REQUEST_URI'))
 	                        )
 	                );
    		}
    		else
    		{
    			return new RedirectResponse('/install');
    		}
    	}
    	
        if ( $state->IsMaintained() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');
    		return new RedirectResponse('/503');
    	}
    }
    
    protected function checkUserAuthorized()
    {
     	$user_it = getSession()->getUserIt();
     	if ( $user_it->getId() > 0 ) return;
     	
 	    $auth_factory = getSession()->getAuthenticationFactory();
 	    if ( !is_object($auth_factory) ) {
 	    	return new RedirectResponse( '/login?page='.$this->getRequest()->server->get('REQUEST_URI') );
 	    }
	
 	    return $auth_factory->credentialsRequired() 
 	        ? new RedirectResponse( '/login?page='.$this->getRequest()->server->get('REQUEST_URI') )
 	        : new RedirectResponse( '/404?redirect='.$this->getRequest()->server->get('REQUEST_URI') );
    }
}