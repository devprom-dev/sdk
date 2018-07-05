<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BaseController extends PageController
{
	public function checkAccess()
	{
	    if ( !\DeploymentState::Instance()->IsReadyToBeUsed() ) return null;
		if ( !getSession()->getUserIt()->IsAdministrator() ) return $this->redirect('/');
		return null;
	}
}