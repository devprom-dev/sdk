<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/project/ProjectMetricsPage.php";

class MetricsController extends PageController
{
    public function pageAction( Request $request )
    {
        $_REQUEST['report'] = $request->get('report');
    	return $this->responsePage( new \ProjectMetricsPage() );
    }
}