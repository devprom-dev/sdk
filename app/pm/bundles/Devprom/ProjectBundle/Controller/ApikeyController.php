<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Devprom\CommonBundle\Service\Project\InviteService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ApiKeyController extends PageController
{
    public function fullAction()
    {
    	return $this->render( 'ProjectBundle:ApiKey:show.html.twig',
                array (
                    'key' => \AuthenticationAPIKeyFactory::getAuthKey(getSession()->getUserIt())
                )
        );
    }

    public function readonlyAction()
    {
        return $this->render( 'ProjectBundle:ApiKey:show.html.twig',
            array (
                'key' => \AuthenticationAPIKeyFactory::getReadOnlyAuthKey(getSession()->getUserIt())
            )
        );
    }

    public function writeonlyAction()
    {
        return $this->render( 'ProjectBundle:ApiKey:show.html.twig',
            array (
                'key' => \AuthenticationAPIKeyFactory::getWriteOnlyAuthKey(getSession()->getUserIt())
            )
        );
    }
}