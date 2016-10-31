<?php

namespace Devprom\CommonBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

include_once SERVER_ROOT_PATH."co/views/Common.php";
include_once SERVER_ROOT_PATH."co/views/ErrorPage.php";
 
class ExceptionController extends PageController
{
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        $_SESSION['exception'] = $exception->getMessage();
        $_SERVER['REQUEST_URI'] = '/404';

    	return $this->responsePage( new \ErrorPage() );
    }
}