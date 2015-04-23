<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\MainController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntryController extends MainController
{
    public function indexAction()
    {
        if ( preg_match('/command/i', $this->getRequest()->getBaseUrl()) ) {
    		return new Response();
    	}
    	
    	if ( getSession()->getUserIt()->getId() < 1 ) {
    		return new RedirectResponse($this->generateUrl('login')); 
    	}
    	
    	return $this->redirect('/pm/my');
    }
}