<?php
namespace Devprom\AdministrativeBundle\Controller;

class WebhookLogController extends BaseController
{
    public function indexAction()
    {
    	if ( is_object($response = $this->checkAccess()) ) return $response;
    	include SERVER_ROOT_PATH.'admin/views/webhooks/WebhookLogPage.php';
    	return $this->responsePage(new \WebhookLogPage);
    }
}