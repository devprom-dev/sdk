<?php

namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Project\ExportService;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/plan/IterationPage.php";

class IterationController extends PageController
{
    public function listAction(Request $request) {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \IterationPage() );
    }
}