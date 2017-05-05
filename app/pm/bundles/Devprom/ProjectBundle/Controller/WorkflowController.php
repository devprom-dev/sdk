<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/workflow/AutoActionPage.php";

class WorkflowController extends PageController
{
	public function autoactionAction(Request $request)
    {
		return $this->responsePage( new \AutoActionPage() );
	}
}