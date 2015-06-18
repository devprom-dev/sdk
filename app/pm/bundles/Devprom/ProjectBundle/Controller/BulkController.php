<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Devprom\CommonBundle\Service\Project\InviteService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";

class BulkController extends PageController
{
    public function formAction()
    {
    	return $this->responsePage( new \BulkPage() );
    }
}