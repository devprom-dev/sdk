<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\MainController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntryController extends MainController
{
    public function indexAction(Request $request)
    {
        if ( preg_match('/command/i', $request->getBaseUrl()) ) {
    		return new Response();
    	}

    	if ( getSession()->getUserIt()->getId() < 1 ) {
    		return new RedirectResponse($this->generateUrl('login'));
    	}

    	return $this->redirect($_SERVER['ENTRY_URL']);
    }
}