<?php
namespace Devprom\WelcomeBundle\Controller\Rest;
use Devprom\WelcomeBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends RestController
{
    public function cgetAction(Request $request)
    {
        try
        {
            throw new \Exception("Not implemented");
        }
        catch( \Exception $e )
        {
            \Logger::getLogger('System')->error($e->getMessage());
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}