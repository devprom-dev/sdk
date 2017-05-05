<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/time/SpentTimePage.php";

class TimeController extends PageController
{
	public function worklogAction(Request $request)
    {
		return $this->responsePage( new \SpentTimePage() );
	}
}