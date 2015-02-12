<?php

include_once SERVER_ROOT_PATH."core/c_command.php";

class ProcessOrder extends CommandForm
{
 	function validate()
 	{
		$this->checkRequired( array('OrderId','OrderInfo') );
		
		return true;
 	}

 	function delete()
 	{
 		$this->replyRedirect('/module/accountclient/failed?ErrorCode='.intval($_REQUEST['ErrorCode']));
 	}
 	
 	function create()
	{
		if ( $_COOKIE['devprom-order-info'] == '' ) $this->delete();
		
		$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
		
		$orderId = $_REQUEST['OrderId'];
		
		if ( $order_info['OrderId'] != $orderId ) $this->delete();

		$securityKey = MERCHANT_KEY;
		
		$baseQuery = "DateTime=".$_REQUEST['DateTime'].
					 "&TransactionID=".$_REQUEST['TransactionID'].
                     "&OrderId=".$orderId.
                     "&Amount=".$order_info['Amount'].	
                     "&Currency=".$order_info['Currency'];

		$queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".$securityKey;

		if ( $_REQUEST['SecurityKey'] != md5($queryWithSecurityKey) ) $this->delete();

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
						)
		);
		
		$this->updateSupportSubscription(
				$order_info['InstallationUID'], 
				$licensed_days, 
				$order_info['LicenseType'], 
				$order_info['Redirect']
		);
		$this->sendMail($query_parms['LicenseKey'], $query_parms['LicenseValue']);
		
		$query_parms['LicenseValue'] = $licensed_days;
		$this->replyRedirect('/module/accountclient/process?'.http_build_query($query_parms));
	}
	
	function replyRedirect( $url )
	{
		$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
		$url_parts = parse_url($order_info['Redirect']);
		
		exit(header('Location: '.$url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].$url));
	}
	
	protected function getLicenseKey( $uid, & $value, $type, $was_license_value, $was_license_key )
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
	
	protected function sendMail( $key, $value )
	{
	    $mail = new HtmlMailbox;

	    $mail->appendAddress(getSession()->getUserIt()->get('Email'));
	    $language = getSession()->getUserIt()->get('Language') == 1 ? 'ru' : 'en';
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/account/resources/'.$language.'/order-confirmation.html');
	    
	    if ( $value < 2 )
	    {
	    	$value = $value.' мес€ц';
	    }

		if ( $value > 1 and $value < 5 )
	    {
	    	$value = $value.' мес€ца';
	    }
	    
		if ( $value > 4 )
	    {
	    	$value = $value.' мес€цев';
	    }
	    
	    $body = preg_replace('/\%value\%/', $value, $body);
	    $body = preg_replace('/\%key\%/', $key, $body);
		
	    $mail->setBody($body);
	    
	    $mail->setSubject( 'ѕродление использовани€ Devprom.ALM' );
	    $mail->setFrom("Devprom Software <".getFactory()->getObject('cms_SystemSettings')->getAll()->get('AdminEmail').">");
	    	
	    $mail->send();
	}
	
	protected function updateSupportSubscription( $iid, $days, $license_type, $redirect_url )
	{
		$payed_till = date('Y-m-d', strtotime($days.' day', strtotime(date('Y-m-j'))));
		
		$service_it = getFactory()->getObject('ServicePayed')->getByRef('VPD', $iid);
		if ( $service_it->getId() > 0 )
		{
			$service_it->object->modify_parms($service_it->getId(), 
					array (
							'PayedTill' => $payed_till 
					)
			);
		}
		else
		{
			$url_parts = parse_url($redirect_url);
			$service_it->object->add_parms(
					array (
							'Caption' => $url_parts['host'],
							'IID' => $iid,
							'PayedTill' => $payed_till,
							'Description' => $license_type
					)
			);
		}
	}
}
