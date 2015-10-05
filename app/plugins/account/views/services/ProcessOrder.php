<?php

define ('SAASSALT', 'b49ca47b46v46c581u3c34dlc0ac85d2');

include_once SERVER_ROOT_PATH."core/c_command.php";
include_once SERVER_ROOT_PATH."cms/c_mail.php";

class ProcessOrder extends CommandForm
{
 	function validate()
 	{
 		Logger::getLogger('Commands')->info('ACCOUNT: '.var_export($_REQUEST, true));
 		
		$this->checkRequired( array('OrderId','OrderInfo') );
		
		return true;
 	}

 	function delete()
 	{
 		$this->replyRedirect('/module/accountclient/failed?ErrorCode='.intval($_REQUEST['ErrorCode']));
 	}
 	
 	function create()
	{
		if ( $_REQUEST['OrderInfo'] == '' ) $this->delete();
		
		$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
		Logger::getLogger('Commands')->info('ORDER: '.var_export($order_info, true));

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

		switch( $order_info['LicenseType'] )
		{
			case 'LicenseTeam':
			case 'LicenseTeamSupported':
			case 'LicenseTeamSupportedCompany':
			case 'LicenseTeamSupportedUnlimited':
				$licensed_days = $order_info['LicenseValue'];
				break;
			default:
				$licensed_days = $order_info['LicenseValue'] * 30;
		}
		$order_info['LicenseValue'] = $licensed_days;

		$license_value = $this->getLicenseValue($order_info);
		$license_key = $this->getLicenseKey($order_info['InstallationUID'], $license_value, $order_info);

		$query_parms = array (
				'InstallationUID' => $order_info['InstallationUID'],
				'LicenseType' => $order_info['LicenseType'],
				'LicenseValue' => $license_value,
				'LicenseKey' => $license_key
		);
		$this->updateSupportSubscription(
				$order_info['InstallationUID'], 
				$licensed_days, 
				$order_info['LicenseType'], 
				$order_info['Redirect']
		);
		$this->sendMail($query_parms['LicenseKey'], round($licensed_days / 30, 0));
		$this->replyRedirect('/module/accountclient/process?'.http_build_query($query_parms));
	}
	
	function replyRedirect( $url )
	{
		$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
		$url_parts = parse_url($order_info['Redirect']);
		
		exit(header('Location: '.$url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].$url));
	}
	
	protected function getLicenseKey( $uid, $value )
	{
		date_default_timezone_set('UTC');
		$today_date = strtotime('-0 day', strtotime(date('Y-m-j')));
		return md5($uid.$value.SAASSALT.date('#2fee3ffY#3fe2a32m-@3@j', $today_date));
	}

	protected function getLicenseValue( $order_info )
	{
		$uid = $order_info['InstallationUID'];
		$was_license_value = $order_info['WasLicenseValue'];
		$was_license_key = $order_info['WasLicenseKey'];
		date_default_timezone_set('UTC');

		$index = 0;
		$left_days = 0;
		while( $index < 365 )
		{
			$date = strtotime('-'.$index.' day', strtotime(date('Y-m-j')));
			if ( md5($uid.$was_license_value.SAASSALT.date('#2fee3ffY#3fe2a32m-@3@j', $date)) == trim($was_license_key) )
			{
				$left_days = abs($was_license_value - $index);
				break;
			}
			$index++;
		}

		$value = $order_info['LicenseValue'];
		$value += $left_days;
		return $value;
	}

	protected function sendMail( $key, $value )
	{
	    $mail = new HtmlMailbox;

	    $mail->appendAddress(getSession()->getUserIt()->get('Email'));
	    $language = getSession()->getUserIt()->get('Language') == 1 ? 'ru' : 'en';
	    
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/account/resources/'.$language.'/order-confirmation.html');
	    
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
	    $mail->setSubject(text('account27'));
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
