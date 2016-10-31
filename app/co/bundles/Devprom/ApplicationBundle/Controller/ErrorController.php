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

class ErrorController extends PageController
{
    public function errorZipAction()
    {
        $error = new ExceptionHandler();

        $response = new Response($error->getDataZip());
        $response->headers->set('Content-Type', 'application/zip');

        return $response;
    }
}