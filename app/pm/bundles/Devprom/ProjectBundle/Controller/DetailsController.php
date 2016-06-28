<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/communications/ProjectLogDetailsPage.php";
include_once SERVER_ROOT_PATH."pm/views/project/WorkloadDetailsPage.php";

class DetailsController extends PageController
{
    public function logAction(Request $request) {
    	return $this->responsePage( new \ProjectLogDetailsPage() );
    }

    public function workloadAction(Request $request) {
        return $this->responsePage( new \WorkloadDetailsPage() );
    }
}