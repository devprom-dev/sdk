<?php

namespace Devprom\ProjectBundle\Controller;

use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Devprom\ProjectBundle\Service\Model\ModelService;

class MentionController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
        try
        {
            $service = new ModelService();
            return $this->handleView(
                $this->view(
                    $service->find(getFactory()->getObject('Mentioned')), 200
                ));
        }
        catch( \Exception $e )
        {
            \Logger::getLogger('System')->error($e->getMessage());
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}