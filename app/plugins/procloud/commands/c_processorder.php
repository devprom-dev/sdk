<?php
 
class ProcessOrder extends CommandForm
{
 	function validate()
 	{
		$this->checkRequired( array('OrderId') );
		
		return true;
 	}
 	
 	function create()
	{
		if ( $_COOKIE['devprom-order-info'] == '' ) $this->replyRedirect('/');
		
		$order_info = JsonWrapper::decode($_COOKIE['devprom-order-info']);
		
		$orderId = $_REQUEST['OrderId'];
		
		if ( $order_info['OrderId'] != $orderId ) $this->replyRedirect('/');

		$currency = "RUB";
		$securityKey = "30cfcab4-ce10-413f-bbfd-4a367823bc1c";
		
		$baseQuery = "DateTime=".$_REQUEST['DateTime'].
					 "&TransactionID=".$_REQUEST['TransactionID'].
                     "&OrderId=".$orderId.
                     "&Amount=".$order_info['Amount'].	
                     "&Currency=".$currency;

		$queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".$securityKey;

		if ( $_REQUEST['SecurityKey'] != md5($queryWithSecurityKey) ) $this->replyRedirect('/');

		$licensed_days = $order_info['LicenseValue'] * 30;
		
		$query_parms = array (
				'InstallationUID' => $order_info['InstallationUID'],
				'LicenseType' => $order_info['LicenseType'],
				'LicenseValue' => $order_info['LicenseValue'],
				'LicenseKey' => 
						$this->getLicenseKey(
								$order_info['InstallationUID'], 
								$licensed_days, 
								$order_info['LicenseType'],
								$order_info['WasLicenseValue'],
								$order_info['WasLicenseKey']
						),
				'autosubmit' => 'yep'
		);
		
		$this->sendMail($query_parms['LicenseKey'], $query_parms['LicenseValue']);
		
		$query_parms['LicenseValue'] = $licensed_days;
		
		$this->replyRedirect($order_info['Redirect'].'?'.http_build_query($query_parms));
	}
	
	function replyRedirect( $url )
	{
		exit(header('Location: '.$url));
	}
	
	function getLicenseKey( $uid, & $value, $type, $was_license_value, $was_license_key )
	{
		define ('SAASSALT', 'b49ca47b46v46c581u3c34dlc0ac85d2');

		$salt = SAASSALT;
		
		date_default_timezone_set('UTC');
		
		$today_date = strtotime('-0 day', strtotime(date('Y-m-j')));

		$index = 0;
		$left_days = 0;
 				
		while( $index < 365 )
		{
			$date = strtotime('-'.$index.' day', strtotime(date('Y-m-j')));			
				
			if ( md5($uid.$was_license_value.$salt.date('#2fee3ffY#3fe2a32m-@3@j', $date)) == trim($was_license_key) )
			{ 
		 		$left_days = abs($was_license_value - $index);
				break;
		 	}
				 			
			$index++;
		}
		
		$value += $left_days;
		
		return md5($uid.$value.$salt.date('#2fee3ffY#3fe2a32m-@3@j', $today_date));
	}
	
	function sendMail( $key, $value )
	{
	    $mail = new HtmlMailbox;

	    $mail->appendAddress(getSession()->getUserIt()->get('Email'));
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/procloud/resources/order-confirmation.html');
	    
	    if ( $value < 2 )
	    {
	    	$value = $value.' месяц';
	    }

		if ( $value > 1 and $value < 5 )
	    {
	    	$value = $value.' месяца';
	    }
	    
		if ( $value > 4 )
	    {
	    	$value = $value.' месяцев';
	    }
	    
	    $body = preg_replace('/\%value\%/', $value, $body);
	    $body = preg_replace('/\%key\%/', $key, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( 'Продление использования Devprom.ALM' );
	    $mail->setFrom("Devprom Software <".getFactory()->getObject('cms_SystemSettings')->getAll()->get('AdminEmail').">");
	    	
	    $mail->send();
	}
}
