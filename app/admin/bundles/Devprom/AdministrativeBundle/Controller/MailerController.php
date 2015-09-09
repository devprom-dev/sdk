<?php

namespace Devprom\AdministrativeBundle\Controller;

use Devprom\AdministrativeBundle\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    	
    	$settings = getFactory()->getObject('MailerSettings');
    	
    	$parms = array();
    	
    	foreach( $settings->getAttributes() as $attribute => $data )
    	{
    		$parms[$attribute] = $request->request->get($attribute);
    	}
    	
    	$settings->modify_parms($settings->getAll()->getId(), $parms);
    	
    	$test_email = $request->request->get("MailTestEmail");
    	
    	if ( $test_email != '' )
    	{
    		$from_address = $parms['AdminEmail'];
    		
    		if ( $from_address == '' )
    		{
    			return $this->replyError(text(1267));
    		}
    		
			$mail_result = mail(
					$test_email, 
					'=?UTF-8?B?'.base64_encode(\IteratorBase::wintoutf8(text(1523))).'?=', 
					'<html>'.PHP_EOL.
		    			'<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head>'.PHP_EOL.
		    			'<body>'.\IteratorBase::wintoutf8(text(1524)).'</body>'.PHP_EOL.
		    		'</html>',
					"Sender: ".$from_address."\r\nFrom: ".$from_address."\r\n".
					"MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n", 
					"-f ".$from_address
    		);
			
			return $this->replySuccess(text(1706).'<br/><br/>'.text(1526));
    	}
    	else
    	{
    		return $this->replyRedirect('/admin/mailer/', text(1706));
    	}
    }
}