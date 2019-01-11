<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Devprom\ProjectBundle\Service\TreeviewModel\FeatureService;
use Devprom\ProjectBundle\Service\TreeviewModel\WikiService;
use Devprom\ProjectBundle\Service\TreeGridViewModel\TreeGridService;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/product/FunctionsPage.php";

class TreeController extends PageController
{
    public function indexAction(Request $request)
    {
    	if ( $request->get('classname') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined but required');
    	}

    	if ( in_array(strtolower($request->get('classname')), array('feature','featureterminal')) ) {
    		$service = new FeatureService($request->get('root'));
    	}
    	else {
    		$service = new WikiService($request->get('classname'), $request->get('root'), $request->query->has('cross'));
    	}
    	
    	return new JsonResponse($service->getData());
    }

    public function gridAction(Request $request)
    {
        $service = new TreeGridService($request->get('classname'));

        switch( $request->get('classname') ) {
            case 'feature':
                $page = new \FunctionsPage();
                $page->getTableRef()->buildFilters();

                $list = new \FunctionList($page->getTableRef()->getObject());
                $list->skipInvisiblePersisters(false);
                $list->setTable($page->getTableRef());

                return new JsonResponse(
                    $service->getTreeGridJsonView($list, $this->getTemplatingEngine(), 'Caption', 'Children', 'ParentFeature')
                );

            default:
                throw $this->createNotFoundException('Class name is undefined but required');
        }
    }
}