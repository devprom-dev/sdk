<?php

namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH."pm/views/ui/PMLastChangesSection.php";

class SectionController extends PageController
{
    public function drawAction(Request $request)
    {
    	if ( $request->query->get('class') == "" ) {
    		throw $this->createNotFoundException('Class name is undefined');
    	}
    	if ( $request->query->get('id') == "" ) {
    		throw $this->createNotFoundException('Objects are undefined');
    	}
    	
    	$class = getFactory()->getClass($request->query->get('class'));
    	if ( !class_exists($class) ) {
    		throw $this->createNotFoundException('Class name doesn\'t exist');
    	}

		switch( $request->get('section') )
		{
			case 'audit':
				$section = new \PMLastChangesSection(
					getFactory()->getObject($class)->getExact($request->query->get('id'))
				);

				ob_start();
				$section->renderBody( $this->getTemplatingEngine() );
				$content = ob_get_contents();
				ob_end_clean();
		}

    	return new Response($content);
    }
}