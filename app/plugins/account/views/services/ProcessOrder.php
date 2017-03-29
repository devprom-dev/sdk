<?php

define ('SAASSALT', 'b49ca47b46v46c581u3c34dlc0ac85d2');

include_once SERVER_ROOT_PATH."core/c_command.php";
include_once SERVER_ROOT_PATH."cms/c_mail.php";
include_once "PayonlineStore.php";
include_once "YandexStore.php";

class ProcessOrder extends CommandForm
{
    function getStore()
    {
        $language = in_array(getSession()->getUserIt()->get('Language'), array('',1)) ? 'ru' : 'en';
        //return new PayonlineStore($language);
        return new YandexStore($language);
    }

    function execute()
    {
        $this->logStart();
        switch( $this->getAction() ) {
            case CO_ACTION_CREATE:
            case 'paymentAviso':
                if ( $this->validate() ) $this->create();
                if ( $this->validate() ) $this->install();
                break;

            case CO_ACTION_MODIFY:
            case 'checkOrder':
                if ( $this->validate() ) $this->modify( $_REQUEST['object_id'] );
                break;

            case CO_ACTION_DELETE:
            case 'cancelOrder':
                if ( $this->validate() ) $this->delete( $_REQUEST['object_id'] );
                break;
        }
        $this->logFinish();
    }

    function validate()
 	{
		$this->checkRequired( array('OrderInfo') );

        $orderInfo = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        $checkSum = $orderInfo['Checksum'];
        $checksumInfo = array (
            'LicenseType' => $orderInfo['LicenseType'],
            'LicenseValue' => $orderInfo['LicenseValue'],
            'InstallationUID' => $orderInfo['InstallationUID'],
            'LicenseOptions' => $orderInfo['LicenseOptions'],
            'LicenseUsers' => $orderInfo['LicenseUsers']
        );
        $validCheckSum = md5(var_export($checksumInfo,true).INSTALLATION_UID);
        if ( $checkSum != $validCheckSum ) {
            Logger::getLogger('Commands')->error('ORDER FAILED: '.var_export($_REQUEST, true));
            Logger::getLogger('Commands')->error('Invalid checksum '.$checkSum.', sould be '.$validCheckSum);
            $this->replyRedirect('/module/accountclient/failed?ErrorCode=9');
        }

		return true;
 	}

 	function delete()
 	{
        Logger::getLogger('Commands')->error('ORDER FAILED: '.var_export($_REQUEST, true));
 		$this->replyRedirect('/module/accountclient/failed?ErrorCode='.intval($_REQUEST['ErrorCode']));
 	}

    function modify()
    {
        $store = $this->getStore();
        if ( $store->validateOrder($_REQUEST) ) {
            Logger::getLogger('Commands')->info('ORDER CHECKED');
            $store->replyOrderOk();
        }
        else {
            Logger::getLogger('Commands')->error('VALIDATION FAILED: '.var_export($_REQUEST, true));
            $store->replyOrderWrong();
        }
    }

 	function create()
	{
        $order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        Logger::getLogger('Commands')->info('ORDER: '.var_export($order_info, true));

        $store = $this->getStore();
        if ( !$store->checkProcessingParms($_REQUEST) ) {
            Logger::getLogger('Commands')->error('SKIP PROCESSING');
            return;
        }
        if ( !$store->validateOrder($_REQUEST) ) {
            Logger::getLogger('Commands')->error('VALIDATION FAILED: '.var_export($_REQUEST, true));
            $store->replyOrderWrong();
        }
        Logger::getLogger('Commands')->info('PROCESSING');

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
				JsonWrapper::encode($query_parms),
				$order_info['Redirect']
		);
		$this->sendMail($order_info['Email'], $order_info['Language'], $query_parms['LicenseKey'], round($licensed_days / 30, 0), $license_value);
        $store->replyProcessingOk(http_build_query($query_parms));
	}

    function install()
    {
        $order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
        Logger::getLogger('Commands')->info('ORDER: '.var_export($order_info, true));

        $service_it = getFactory()->getObject('ServicePayed')->getByRef('VPD', $order_info['InstallationUID']);
        if ( $service_it->getId() < 1 ) {
            Logger::getLogger('Commands')->error('LICENSE WAS NOT FOUND: '.$order_info['InstallationUID']);
            $this->delete();
        }

        $query_parms = JsonWrapper::decode($service_it->getHtmlDecoded('Description'));
        $this->getStore()->replyLicenseInstalled(http_build_query($query_parms));
    }

	function replyRedirect( $url )
	{
		$order_info = JsonWrapper::decode(urldecode($_REQUEST['OrderInfo']));
		$url_parts = parse_url($order_info['Redirect']);
		
		exit(header('Location: '.$url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].$url));
	}
	
	public function getLicenseKey( $uid, $value )
	{
		date_default_timezone_set('UTC');
		$today_date = strtotime('-0 day', strtotime(date('Y-m-j')));
		return md5($uid.$value.SAASSALT.date('#2fee3ffY#3fe2a32m-@3@j', $today_date));
	}

    public function getLicenseValue( $order_info )
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

	protected function sendMail( $email, $language, $key, $value, $parameters )
	{
	    $mail = new HtmlMailbox;
	    $mail->appendAddress($email);

	    $language = in_array($language,array('','1')) ? 'ru' : 'en';
	    $body = file_get_contents(SERVER_ROOT_PATH.'plugins/account/resources/'.$language.'/order-confirmation.html');
	    
	    if ( $value < 2 ) {
	    	$value = $value.' месяц';
	    }

		if ( $value > 1 and $value < 5 ) {
	    	$value = $value.' месяца';
	    }
	    
		if ( $value > 4 ) {
	    	$value = $value.' месяцев';
	    }
	    
	    $body = preg_replace('/\%value\%/', $value, $body);
	    $body = preg_replace('/\%key\%/', $key, $body);
        $body = preg_replace('/\%license\%/', $parameters, $body);

	    $mail->setBody(mb_convert_encoding($body, "cp1251", "utf-8"));
	    $mail->setSubject(mb_convert_encoding(text('account27'), "cp1251", "utf-8"));
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
                    'PayedTill' => $payed_till,
                    'Description' => $license_type
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
