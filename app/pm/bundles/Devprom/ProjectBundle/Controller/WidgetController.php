<?php

namespace Devprom\ProjectBundle\Controller;
use Devprom\CommonBundle\Controller\PageController;
use Symfony\Component\HttpFoundation\Request;

include_once SERVER_ROOT_PATH . "pm/views/ui/Common.php";
include_once SERVER_ROOT_PATH . "pm/views/settings/ShareModuleSettingsPage.php";
include_once SERVER_ROOT_PATH . "pm/views/project/SelectProjectPage.php";
include_once SERVER_ROOT_PATH . "pm/views/settings/FormFieldSelectEntityPage.php";

class WidgetController extends PageController
{
    public function shareAction(Request $request) {
        return $this->responsePage( new \ShareModuleSettingsPage() );
    }

    public function projectAction(Request $request) {
        return $this->responsePage( new \SelectProjectPage() );
    }

    public function entityAction(Request $request) {
        return $this->responsePage( new \FormFieldSelectEntityPage() );
    }
}