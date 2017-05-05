<?php
namespace Devprom\ProjectBundle\Controller\Rest;

use Devprom\ProjectBundle\Controller\Rest\RestController;
use Symfony\Component\HttpFoundation\Request;

class ItemController extends RestController
{
    function getEntity(Request $request)
    {
        return $request->get('class');
    }
}