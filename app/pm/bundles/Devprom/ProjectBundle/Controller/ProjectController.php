<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Project\ExportService;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/communications/WhatsNewPage.php";
include_once SERVER_ROOT_PATH . "pm/views/project/ProjectDashboardPage.php";
include_once SERVER_ROOT_PATH . "pm/views/product/DeliveryPage.php";

class ProjectController extends PageController
{
    public function exportAction(Request $request)
    {
    	$project_it = getSession()->getProjectIt();

    	$service = new ExportService();
    	$service->execute($project_it);
    	
        return new RedirectResponse('/pm/'.$project_it->get('CodeName'));
    }

    public function whatsnewAction(Request $request)
    {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \WhatsNewPage() );
    }

    public function listAction(Request $request)
    {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \ProjectDashboardPage() );
    }

    public function roadmapAction(Request $request)
    {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \DeliveryPage() );
    }
}