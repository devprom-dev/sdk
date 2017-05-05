<?php

namespace Devprom\ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WidgetController extends Controller
{
    public function shortcutAction(Request $request)
    {
    	return $this->render( 'ApplicationBundle:UI:shortcuts.html.twig',
            array(
                'version' => $_SERVER['APP_VERSION'],
                'language' => in_array($request->get('language'), array('en','ru'))
                                ? $request->get('language')
                                : strtolower(getSession()->getLanguageUid())
    		)
        );
    }
}