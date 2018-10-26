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

    public function supportAction(Request $request)
    {
        $language = in_array($request->get('language'), array('en','ru'))
                        ? $request->get('language')
                        : strtolower(getSession()->getLanguageUid());

        $support_url = defined('HELP_SUPPORT_URL')
                        ? HELP_SUPPORT_URL
                        : 'http://support.devprom.ru/issue/new';

        if ( defined('METRICS_VISIBLE') && METRICS_VISIBLE ) {
            $metrics_text = str_replace('%1', \MetricsServer::Instance()->getDuration(), text(1067));
            $metrics_text = str_replace('%2', \MetricsClient::Instance()->getDuration('clscript'), $metrics_text);
        }

        return $this->render( 'ApplicationBundle:UI:'.$language.'/support.html.twig',
            array(
                'version' => $_SERVER['APP_VERSION'],
                'language' => $language,
                'support_url' => $support_url,
                'license_name' => $_SERVER['LICENSE'],
                'current_version' => $_SERVER['APP_VERSION'],
                'metrics_text' => $metrics_text
            )
        );
    }
}