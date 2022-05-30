<?php
namespace Devprom\ProjectBundle\Controller;

use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/time/SpentTimePage.php";

class TimeController extends PageController
{
	public function worklogAction(Request $request)
    {
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        if ( $_REQUEST['report'] == 'chart' ) {
            unset($_REQUEST['report']);
            $_REQUEST['view'] = 'chart';
        }
		return $this->responsePage( new \SpentTimePage() );
	}

    public function chartAction(Request $request)
    {
        $_REQUEST['view'] = 'chart';
        if ( $request->get('report') != '' ) {
            $_REQUEST['report'] = $request->get('report');
        }
        return $this->responsePage( new \SpentTimePage() );
    }
}