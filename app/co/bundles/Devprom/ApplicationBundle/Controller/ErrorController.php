<?php

namespace Devprom\ApplicationBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper;
use core\classes\ExceptionHandler;

if ( !class_exists('CoPage', false) ) include SERVER_ROOT_PATH."co/views/Common.php";
include SERVER_ROOT_PATH."co/views/ErrorPage.php";

class ErrorController extends PageController
{
    public function errorAction()
    {
        $response = $this->responsePage( new \ErrorPage() );
        
        $parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);
        
        $response->headers->set('Status', trim($parts[0],'/'));
        
        return $response;
    }

    public function errorZipAction()
    {
        $error = new ExceptionHandler();

        $response = new Response($error->getDataZip());
        
        $response->headers->set('Content-Type', 'application/zip');

        return $response;
    }
}