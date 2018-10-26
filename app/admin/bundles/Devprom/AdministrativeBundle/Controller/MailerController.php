<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Devprom\CommonBundle\Doctrine\DBAL\Listener\PostConnectListener;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

include_once SERVER_ROOT_PATH . 'admin/install/Installable.php';
include_once SERVER_ROOT_PATH . 'admin/install/ClearCache.php';
include_once SERVER_ROOT_PATH . 'tasks/commands/TaskCommand.php';
include_once SERVER_ROOT_PATH . "tasks/commands/c_processemailqueue.php";

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

        $mapper = new \ModelDataTypeMapper();
        $mapper->map( $settings, $parms );
    	$settings->modify_parms($settings->getAll()->getId(), $parms);

		$command = new \ClearCache();
		$command->install();

    	$test_email = $request->request->get("MailTestEmail");
    	if ( $test_email != '' )
    	{
			$settings_it = getFactory()->getObject('SystemSettings')->getAll();

			$mail = new \HtmlMailbox;
			if ( $settings_it->get('AdminEmail') != '' ) {
				$mail->setFrom($settings_it->getDisplayName() . ' <'.$settings_it->get('AdminEmail').'>');
			}
			else {
				$mail->setFromUser(getSession()->getUserIt());
			}
			$mail->appendAddress($test_email);
			$mail->setSubject(text(1523));
			$mail->setBody(text(1524));
			$mail->send();

			file_get_contents(\EnvironmentSettings::getServerUrl().'/tasks/command.php?class=runjobs&job=4');

			$text = '<br/><br/>'.text(1526);
            $logUrl = '';

			$log_it = getFactory()->getObject('SystemLog')->getAll();
			while( !$log_it->end() ) {
				if ( substr($log_it->get('Caption'), 0, 4) == 'mail' ) {
				    // search for recent log
				    $logUrl = $log_it->getViewUrl();
				}
				$log_it->moveNext();
			}
			if ( $logUrl != '' ) {
                $text = preg_replace('/\%1/', $logUrl, $text);
            }

			return $this->replySuccess(text(1706).$text);
    	}
    	else
    	{
    		return $this->replyRedirect('/admin/mailer/', text(1706));
    	}
    }
}