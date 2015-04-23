<?php

namespace Devprom\ApplicationBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends PageController
{
    public function configAction()
    {
        return $this->render('ApplicationBundle:Menu:config.html.twig', array());
    	//return $this->responsePage(new \CoPage());
    }
}