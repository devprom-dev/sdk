<?php
namespace Devprom\ProjectBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/dashboard/DashboardPage.php";

class DashboardController extends PageController
{
    public function indexAction(Request $request) {
        return $this->responsePage( new \DashboardPage() );
    }
}