<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/attachments/AttachmentsPage.php";

class AttachmentsController extends PageController
{
    public function pageAction()
    {
    	return $this->responsePage( new \AttachmentsPage() );
    }
}