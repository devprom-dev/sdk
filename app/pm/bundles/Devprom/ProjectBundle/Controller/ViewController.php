<?php
namespace Devprom\ProjectBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeviewModel\HierarchyService;
use Devprom\ProjectBundle\Service\TreeviewModel\WikiService;
use Devprom\ProjectBundle\Service\TreeGridViewModel\TreeGridService;

class ViewController extends PageController
{
    public function formAction(Request $request)
    {
    	if ( $request->get('classname') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}
        if ( $request->get('objectid') == "" ) {
            throw $this->createNotFoundException('Object id is undefined but required');
        }

        $objectIt = getFactory()->getObject($request->get('classname'))
            ->getExact($request->get('objectid'));

        if ( $objectIt->object instanceof \ChangeLog ) {
            $objectIt = $objectIt->getObjectIt();
        }

        if ( $objectIt->object->getEntityRefName() == 'pm_Project' ) {
            return new Response();
        }
        if ( $objectIt->getId() == '' ) {
            return new Response();
        }

    	return new RedirectResponse($objectIt->getViewUrl() . '&attributesonly=true');
    }
}