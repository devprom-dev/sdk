<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MainController extends Controller
{
    public function indexAction()
    {
        if ( preg_match('/command/i', $this->getRequest()->getBaseUrl()) )
    	{
    		return new Response();
    	}
    	
    	if ( getSession()->getUserIt()->getId() < 1 )
    	{
    		return new RedirectResponse($this->generateUrl('login')); 
    	}
    	
    	return $this->redirect('/pm/my');
    }

    protected function checkDeploymentState()
    {
    	$request = $this->getRequest();
    	
    	// check if an update is installing then skip controlling of deployment state
    	if ( preg_match('/update/i', $request->getQueryString()) ) return;

    	$state = getFactory()->getObject('DeploymentState');
    	
    	if ( !$state->IsReadyToBeUsed() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');

    		return new RedirectResponse('/install');
    	}
    	
        if ( $state->IsMaintained() )
    	{
    		$this->get('router')->getGenerator()->getContext()->setBaseUrl('');

    		return new RedirectResponse('/503');
    	}
    }
    
    protected function checkUserAuthorized()
    {
        $response = $this->checkDeploymentState();
        
        if ( is_object($response) ) return $response;
        
     	$session = getSession();
     	
     	$user_it = $session->getUserIt();
     	
     	if ( $user_it->getId() > 0 ) return;
     	
 	    $request = $this->getRequest();
 	    
 	    $auth_factory = $session->getAuthenticationFactory();
 	    
 	    if ( !is_object($auth_factory) )
 	    {
 	    	return new RedirectResponse( '/404?redirect='.$request->server->get('REQUEST_URI') );
 	    }
	
 	    return $auth_factory->credentialsRequired() 
 	        ? new RedirectResponse( 
 	                $this->generateUrl('login', 
 	                        array('page' => $request->server->get('REQUEST_URI'))
 	                        )
 	                )
 	        : new RedirectResponse( '/404?redirect='.$request->server->get('REQUEST_URI') );
    }
}