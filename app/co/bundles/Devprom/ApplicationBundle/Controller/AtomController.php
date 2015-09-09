<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ApplicationBundle\Service\Atom\BlogService;

class AtomController extends Controller
{
    public function newsAction(Request $request)
    {
    	if ( $request->get('key') == "" )
    	{
    		throw $this->createNotFoundException('Key is undefined');
    	}
    	
    	$service = new BlogService;
 	
        $response = new Response($service->replyAtom($request->get('key')));
        
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }
}