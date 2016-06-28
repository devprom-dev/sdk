<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\Network\NetworkService;

class NetworkController extends Controller
{
    public function jsonAction(Request $request)
    {
    	if ( $request->get('classname') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}
		if ( $request->get('object') == "" ) {
			throw $this->createNotFoundException('Object is undefined but required');
		}

  		$service = new NetworkService(getSession(), $request->get('classname'), $request->get('object'));
    	return new JsonResponse($service->getVisData());
    }
}