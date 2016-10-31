<?php

namespace Devprom\WelcomeBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use core\classes\ExceptionHandler;

if ( !class_exists('CoPage', false) ) include SERVER_ROOT_PATH."co/views/Common.php";
include_once SERVER_ROOT_PATH."co/views/ErrorPage.php";

class ErrorController extends PageController
{
    public function errorAction()
    {
        $response = $this->responsePage( new \ErrorPage() );

        $parts = preg_split('/\?/', $_SERVER['REQUEST_URI']);
        $response->headers->set('Status', trim($parts[0],'/'));

        return $response;
    }
}