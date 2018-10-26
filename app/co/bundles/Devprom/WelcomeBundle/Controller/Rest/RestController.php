<?php
namespace Devprom\WelcomeBundle\Controller\Rest;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\FOSRestController;

abstract class RestController extends FOSRestController implements ClassResourceInterface
{
}
