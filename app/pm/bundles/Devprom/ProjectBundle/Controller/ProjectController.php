<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Project\ExportService;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/communications/WhatsNewPage.php";
include_once SERVER_ROOT_PATH . "pm/views/project/ProjectDashboardPage.php";

class ProjectController extends PageController
{
    public function exportAction(Request $request)
    {
    	$project_it = getSession()->getProjectIt();

    	$service = new ExportService();
    	$service->execute($project_it);
    	
        return new RedirectResponse('/pm/'.$project_it->get('CodeName'));
    }

    public function whatsnewAction(Request $request) {
        return $this->responsePage( new \WhatsNewPage() );
    }

    public function listAction(Request $request) {
        return $this->responsePage( new \ProjectDashboardPage() );
    }
}