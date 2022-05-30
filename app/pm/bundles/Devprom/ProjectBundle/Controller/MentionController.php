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
    public function cgetAction(Request $request)
    {
        try
        {
            $mentioned = getFactory()->getObject('Mentioned');

            $className = getFactory()->getClass($request->get('class'));
            if ( class_exists($className) ) {
                $mentionedObject = getFactory()->getObject($className);
                if ( $mentionedObject instanceof \Comment ) {
                    if ( $mentionedObject->IsReference('ObjectId') ) {
                        $mentioned->setAttributesObject(
                            $mentionedObject->getAttributeObject('ObjectId'));
                    }
                }
                else {
                    $mentioned->setAttributesObject($mentionedObject);
                }
            }

            $service = new ModelService();
            return $this->handleView(
                $this->view(
                    $service->find($mentioned), 200
                ));
        }
        catch( \Exception $e )
        {
            \Logger::getLogger('System')->error($e->getMessage());
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}