<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\ApplicationBundle\Controller\PageController;
use Devprom\CommonBundle\Service\Project\InviteService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH."pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH."pm/views/project/InvitationPage.php";

class InvitationController extends PageController
{
    public function formAction()
    {
    	return $this->responsePage( new \InvitationPage() );
    }

    public function formProcessAction()
    {
    	$emails = preg_split('/,/', $this->getRequest()->request->get('Addressee'));
    	
    	if ( count($emails) < 1 ) return;

    	$service = new InviteService($this, getSession());
    	
    	return new Response($service->inviteByEmails($emails));
    }
}