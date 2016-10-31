<?php

namespace Devprom\ApplicationBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends PageController
{
    public function logoffAction(Request $request)
    {
        \SessionBuilder::Instance()->close();

        $redirect = $request->request->get('redirect') == ''
            ? $request->getSession()->get('redirect')
            : $request->request->get('redirect');

        if ( $redirect != '' ) {
            return new RedirectResponse('/login?redirect='.\SanitizeUrl::parseUrl($redirect));
        }
        else {
            return new RedirectResponse('/login');
        }
    }
}