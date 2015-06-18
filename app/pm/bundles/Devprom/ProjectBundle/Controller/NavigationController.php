<?php

namespace Devprom\ProjectBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;

include SERVER_ROOT_PATH . '/pm/views/ui/Common.php';
include SERVER_ROOT_PATH . "pm/views/settings/MenuCustomizationPage.php";

class NavigationController extends PageController
{
    public function indexAction()
    {
		return $this->responsePage(new \MenuCustomizationPage());
    }
}