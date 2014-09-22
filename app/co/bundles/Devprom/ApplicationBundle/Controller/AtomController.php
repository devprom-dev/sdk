<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ApplicationBundle\Service\Atom\BlogService;

class AtomController extends Controller
{
    public function newsAction()
    {
    	if ( $this->getRequest()->get('key') == "" )
    	{
    		throw $this->createNotFoundException('Key is undefined');
    	}
    	
    	$service = new BlogService;
 	
        $response = new Response($service->replyAtom($this->getRequest()->get('key')));
        
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }
}