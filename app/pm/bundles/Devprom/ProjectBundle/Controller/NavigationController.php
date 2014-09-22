<?php

/*
 * @author Vasiliy Pedak truvazia@gmail.com
 */

namespace Devprom\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\Templating\PhpEngine,
    Symfony\Component\Templating\TemplateNameParser,
    Symfony\Component\Templating\Loader\FilesystemLoader,
    Symfony\Component\Templating\Helper\SlotsHelper,
    Symfony\Bundle\FrameworkBundle\Templating\Helper\RouterHelper;

include SERVER_ROOT_PATH . '/pm/views/ui/Common.php';
include SERVER_ROOT_PATH . "pm/views/settings/MenuCustomizationPage.php";

class NavigationController extends Controller {

    public function indexAction()
    {
		return $this->responsePage(new \MenuCustomizationPage());
    }

    protected function responsePage($page)
    {
		$templating = new PhpEngine(
			    new TemplateNameParser(), 
			    new FilesystemLoader(SERVER_ROOT_PATH . '/templates/views/%name%'), 
			    array(
					new SlotsHelper(),
					new RouterHelper($this->get('router')->getGenerator()),
					$this->container->get('assetic.helper.dynamic')
			    )
		);
	
		ob_start();
	
		$page->render($templating);
	
		$content = ob_get_contents();
	
		ob_end_clean();

		return new Response($content);
    }
}