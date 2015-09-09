<?php
/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */
namespace Devprom\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface,
    FOS\RestBundle\Controller\FOSRestController,
	Devprom\ProjectBundle\Service\Navigation\ModuleService;

/**
 * Pages rest getway. 
 */
class PagesController extends FOSRestController implements ClassResourceInterface
{
    public function cgetAction()
    {
    	$service = new ModuleService();
    	
        return $this->handleView($this->view($service->getModules(), 200));
    }
}