<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH . 'admin/install/Installable.php';
include_once SERVER_ROOT_PATH . 'admin/install/ClearCache.php';

class MailerController extends BaseController
{
    public function indexAction()
    {
    	if ( is_object($response = $this->checkAccess()) ) return $response;
    	
    	include SERVER_ROOT_PATH.'admin/views/mailer/MailerPage.php';
    	
    	return $this->responsePage(new \MailerPage);
    }
    
    public function storeAction(Request $request)
    {
    	if ( is_object($response = $this->checkAccess()) ) return $response;
    	
    	$parms = array();

		$settings = getFactory()->getObject('MailerSettings');
    	foreach( $settings->getAttributes() as $attribute => $data ) {
    		$parms[$attribute] = $request->request->get($attribute);
    	}
    	
    	$settings->modify_parms($settings->getAll()->getId(), $parms);

		$command = new \ClearCache();
		$command->install();
		getCheckpointFactory()->getCheckpoint('CheckpointSystem')->checkOnly(array('CheckpointWindowsSMTP'));

    	$test_email = $request->request->get("MailTestEmail");
    	if ( $test_email != '' )
    	{
			$mail = new \HtmlMailbox;
			$mail->setFromUser(getSession()->getUserIt());
			$mail->appendAddress($test_email);
			$mail->setSubject(text(1523));
			$mail->setBody(text(1524));
			$mail->send();

			return $this->replySuccess(text(1706).'<br/><br/>'.text(1526));
    	}
    	else
    	{
    		return $this->replyRedirect('/admin/mailer/', text(1706));
    	}
    }
}