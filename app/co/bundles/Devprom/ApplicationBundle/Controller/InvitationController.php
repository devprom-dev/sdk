<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;

include_once SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/InvitationPage.php";

class InvitationController extends PageController
{
    public function formAction()
    {
    	return $this->responsePage( new \InvitationPage($this) );
    }
}