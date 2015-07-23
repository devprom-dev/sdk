<?php

include_once SERVER_ROOT_PATH."core/c_command.php";
include_once SERVER_ROOT_PATH."cms/c_mail.php";

class GetLicenseKey extends CommandForm
{
 	function validate()
 	{
		$this->checkRequired( array('InstallationUID', 'LicenseType') );
		
		$product_it = $this->getProduct($_REQUEST['LicenseType']);
		if ( $product_it->getId() == '' ) $this->replyError( text('account28') );
		if ( $product_it->get('ValueName') != '' )
		{
			$this->checkRequired(array('LicenseValue'));
			if ( intval($_REQUEST['LicenseValue']) < 1 ) $this->replyError( text('account19') );
		}
		
 		if ( $_REQUEST['Email'] != '' && $_REQUEST['ExistPassword'] != '' )
		{
			$user_it = getFactory()->getObject('User')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('Email', $_REQUEST['Email'])
					)
			);
			
			while( !$user_it->end() )
			{
				if ( $user_it->get('Password') == $user_it->object->getHashedPassword($_REQUEST['ExistPassword']) )
				{
					$this->updateCustomer( $user_it,
							$_REQUEST['InstallationUID'], 
							$_REQUEST['LicenseType']
					);
					break;
				}
				$user_it->moveNext();
			}
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $model_factory;

 	 	if ( !getSession()->getUserIt()->IsReal() ) $this->replyError( text('account18') );
 	 	
 	 	$product_it = $this->getProduct($_REQUEST['LicenseType']);
 	 	$required = $product_it->get('RequiredFields');
 	 	
	 	if ( is_array($required) && in_array('Aggreement', $required) && !in_array(strtolower($_REQUEST['Aggreement']), array('on', 'y')) )
		{
			$this->replyError( text('account21') ); 
		}
 	 	
		$user_it = getSession()->getUserIt();
		
		if ( ($product_it->get('PriceRUB') != '' || $product_it->get('PriceUSD') != '') && $_REQUEST['LicenseKey'] == '' )
		{
			$this->redirectToStore( $user_it->get('Email') );
		}
		
		$license_data = array();
		
		$license = $model_factory->getObject('LicenseData');
		
		$license_it = $license->getAll();
		
		while ( !$license_it->end() )
		{
		    $check_alt_key = $license_it->get('uid') == $_REQUEST['InstallationUID'] 
		        && $license_it->get('type') == $_REQUEST['LicenseType'];
		    
			if ( $check_alt_key )
			{
				$license_data = $license_it->getData();
				
				break;
			}
			
			$license_it->moveNext();
		}
		
		if ( $license_data['key'] == '' )
		{
			switch ( $_REQUEST['LicenseType'] )
			{
				case 'LicenseTeam':
					$license_data['key'] = $this->getTeamLicense($_REQUEST['InstallationUID']);
					break;
	
				case 'LicenseEnterprise':
					$settings = $model_factory->getObject('cms_SystemSettings');
			 		$settings_it = $settings->getAll();
					
			   		$mail = new HtmlMailbox;
		   			$mail->appendAddress('marketing@devprom.ru');
			   		
		   			$body = 'Пользователь %1 (%4) запросил лицензионный ключ для Devprom.ALM:<br/><br/>Идентификатор инсталляции: %2<br/>Пользователей:%3<br/><br/>%5<br/>';
		   			
		   			$body = str_replace('%1', $user_it->getDisplayName(), $body);
		   			$body = str_replace('%4', $user_it->get('Email'), $body);
		   			$body = str_replace('%2', $_REQUEST['InstallationUID'], $body);
		   			$body = str_replace('%3', $_REQUEST['LicenseValue'], $body);
		   			$body = str_replace('%5', $user_it->getHtmlDecoded('Description'), $body);
		   			
			   		$mail->setBody($body);
			   		$mail->setSubject( 'Запрос лицензии Devprom.ALM: '.$_REQUEST['LicenseType'] );
			   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
					
			   		$mail->send();
					
			   		$license_data['key'] = '#';
			   		break;
	
				case 'LicenseTrial':
					$license_data['key'] = $this->getTrialLicense($_REQUEST['InstallationUID'], $_REQUEST['LicenseValue']);
					break;
					
				default:
            		$license_data['key'] = $_REQUEST['LicenseKey'];
			}
			
			$license_data['uid'] = $_REQUEST['InstallationUID'];
			$license_data['type'] = $_REQUEST['LicenseType'];
			$license_data['value'] = $_REQUEST['LicenseValue'];
		}
		else
		{
			$_REQUEST['LicenseValue'] = $license_data['value']; 
		}

		if ( $license_it->end() )
		{
		    $license_it = $license->getByUser($user_it->getId());
		}
		
		$license_it->modify( $license_data );
		
		if ( $license_data['key'] == '#' )
		{
			$this->replySuccess('Ваш запрос передан в отдел продаж, наши сотрудники свяжутся с вами в течение одного рабочего дня.');
		}
		else
		{
		    $settings = $model_factory->getObject('cms_SystemSettings');
		    $settings_it = $settings->getAll();
		    	
		    $mail = new HtmlMailbox;
		    $mail->appendAddress('marketing@devprom.ru');
		    
		    $body = 'Пользователь %1 (%4) получил лицензионный ключ.<br/><br/>Идентификатор инсталляции: %2<br/>Пользователей:%3<br/><br/>%5<br/>';
		    
		    $body = str_replace('%1', $user_it->getDisplayName(), $body);
		    $body = str_replace('%4', $user_it->get('Email'), $body);
		    $body = str_replace('%2', $license_data['uid'], $body);
		    $body = str_replace('%3', $license_data['value'], $body);
		    $body = str_replace('%5', $user_it->getHtmlDecoded('Description'), $body);
		    
		    $mail->setBody($body);
		    $mail->setSubject( 'Генерация лицензии: '.$license_data['type'] );
		    $mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		    	
		    $mail->send();
		    
		    $url = '?LicenseType='.$_REQUEST['LicenseType']
		        .'&InstallationUID='.$_REQUEST['InstallationUID']
		        .'&key=show';
		    
		    $url .= '&Redirect='.$_REQUEST['Redirect'];
		    
			$this->replyRedirect( $url );
		}
	}
	
	function exportToCRM( $user_it, $license_data )
	{
		$url_parts = parse_url(urldecode($_REQUEST['Redirect']));
		
		$owner = 'evgeny.savitsky@devprom.ru';
		
		$parts = preg_split('/\n/', $user_it->getHtmlDecoded('Description'));

		$organization .= IteratorBase::wintoutf8(trim($parts[0]).', '.$url_parts['host'].', '.$license_data['uid']); 

		$data = array (
				'item_type' => 'org',
				'name' => $organization,
				'owner' => $owner,
				'visible_to' => '0'
		);

		$body = wordwrap(JsonWrapper::encode($data), 70, " ");
		
		Logger::getLogger('Commands')->info($body);

		$headers = '';
		
		$headers .= "From:".$owner;
		
		mail('12.141209@dropbox.pipedrive.com', 'organization', $body, $headers);
		
		$data = array (
				'item_type' => 'deal',
				'title' => $license_data['type'].': '.IteratorBase::wintoutf8($user_it->getHtmlDecoded('Description')),
				'organization' => $organization,
				'value' => $license_data['value'] * 9000,
				'currency' => 'RUB',
				'owner' => $owner,
				'visible_to' => '0',
				'person' => array (
						'name' => IteratorBase::wintoutf8($user_it->getDisplayName()),
						'email' => IteratorBase::wintoutf8($user_it->get('Email')),
						'organization' => $organization,
						'phone' => IteratorBase::wintoutf8($user_it->get('Phone'))
				)		
		);

		$body = wordwrap(JsonWrapper::encode($data), 70, " ");
		
		Logger::getLogger('Commands')->info($body);

		$headers = '';
		
		$headers .= "From:".$owner;
		
		mail('12.141209@dropbox.pipedrive.com', 'deal', $body, $headers);
	}
	
	function modify()
	{
		if ( $_REQUEST['Email'] != '' )
		{
			$this->resetCustomerPassword($_REQUEST['Email']);
			$this->replySuccess(str_replace('%1', $_REQUEST['Email'], text('account24')));
		}
	}
	
	function delete()
	{
		global $_REQUEST;

		if ( !getSession()->getUserIt()->IsReal() ) $this->replyError( text('account18') );
		
		$url = $_REQUEST['Redirect'].'?LicenseType='.$_REQUEST['LicenseType'].
			'&LicenseValue='.$_REQUEST['LicenseValue'].'&LicenseKey=';
		
		$this->replyRedirect( $url );
	}
	
	function getTeamLicense( $uid )
	{
 		define ('SALT_', '682FEE73-1B33-4266-9192-474F5D59405D');

 		return md5($uid.SALT_);
	}
	
	function getTrialLicense( $uid, $users )
	{
 		define ('_SALT', 'b49ca47b46846c581f3c34d9c0ac85d2');

 		return md5($uid.$users._SALT.date('#221Y#332m-@j'));
	}
	
	function redirectToStore( $email )
	{
		$store_parms = $this->getStoreParameters($_REQUEST['LicenseType']);
		
		$merchantId = MERCHANT_ID;
		$securityKey = MERCHANT_KEY;
		$currency = $store_parms['Currency'];
		
    	$amount = round($_REQUEST['LicenseValue'] * $store_parms['Price'], 0); 
		$amount .= ".00"; 

		$orderId = abs(crc32($_REQUEST['InstallationUID'].date('Y-m-d H:s:i')));
		
		$baseQuery = "MerchantId=".$merchantId.
                     "&OrderId=".$orderId.
                     "&Amount=".$amount.
                     "&Currency=".$currency;

		$queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".$securityKey;

		$hash = md5($queryWithSecurityKey);
		$url_parts = parse_url($_REQUEST['Redirect']);

		$clientQuery = $baseQuery."&SecurityKey=".$hash;
		$clientQuery .= "&Email=".$email;
		$clientQuery .= "&FailUrl=".urlencode($url_parts['scheme'].'://'.$url_parts['host'].':'.$url_parts['port'].'/module/accountclient/failed');

        $license_value = json_decode(urldecode($_REQUEST['WasLicenseValue']), true);
        if ( is_null($license_value) ) $license_value = $_REQUEST['WasLicenseValue'];

		$order_info = array (
				'LicenseType' => $_REQUEST['LicenseType'],
				'LicenseValue' => $_REQUEST['LicenseValue'],
				'WasLicenseKey' => urlencode(urlencode($_REQUEST['WasLicenseKey'])),
				'WasLicenseValue' => $license_value,
				'InstallationUID' => $_REQUEST['InstallationUID'],
				'LicenseScheme' => $_REQUEST['LicenseScheme'],
                'LicenseOptions' => $store_parms['Options'],
				'Redirect' => $_REQUEST['Redirect'],
				'Amount' => $amount,
				'OrderId' => $orderId,
				'Currency' => $currency
		);
		$clientQuery .= "&OrderInfo=".urlencode(JsonWrapper::encode($order_info));

		$paymentFormAddress = $store_parms['Url'].$clientQuery;
		
		$this->replyRedirect($paymentFormAddress);
	}
	
	function getStoreParameters( $licence_type )
	{
		if ( in_array(getSession()->getUserIt()->get('Language'), array('',1)) ) {
			$parms = array (
				'Currency' => 'RUB',
				'Url' => "https://secure.payonlinesystem.com/ru/payment/?"
			);
			$price_field = 'PriceRUB';
		}
		else {
			$parms = array (
				'Currency' => 'USD',
				'Url' => "https://secure.payonlinesystem.com/en/payment/?"
			);
			$price_field = 'PriceUSD';
		}
		$product_it = $this->getProduct($licence_type);
		$parms['Price'] = intval($product_it->get($price_field));
        $selected_options = array();
		if ( is_array($product_it->get('Options')) ) {
			foreach( $product_it->get('Options') as $option ) {
				if ( array_key_exists($licence_type.'Option_'.$option['OptionId'], $_REQUEST) || $_REQUEST['LicenseScheme'] == '' ) {
					$parms['Price'] += intval($option[$price_field]);
                    $selected_options[] = $option['OptionId'];
				}
                if ( $option[$price_field] == '' ) {
                    $selected_options[] = $option['OptionId'];
                }
			}
		}
        $parms['Options'] = join(',', $selected_options);
		return $parms;
	}

	protected function getProduct($licence_type)
	{
		$products = array (
			new AccountProduct(),
			new AccountProductSaas(),
			new AccountProductDevOps()
		);

		foreach( $products as $product )
		{
			$iterator = $product->getExact($licence_type);
			if ( $iterator->getId() != '' ) return $iterator;
		}

		return $products[0]->getEmptyIterator();
	}
	
	protected function updateCustomer( $user_it, $uid, $type )
	{
		getFactory()->getObject('AccountLicenseData')->modify_parms( $user_it->getId(),
				array (
						'uid' => $uid,
						'type' => $type
				)
		);
		
		getSession()->open( $user_it );
	}
	
	protected function resetCustomerPassword( $email )
	{
		$user = getFactory()->getObject('User');
		$user->setNotificationEnabled(false);
		
		$user_it = $user->getRegistry()->Query(
				array (
						new FilterAttributePredicate('Email', $email)
				)
		);
		
		$new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789.-+{}[]()"),0,16);
		
		$user->modify_parms( $user_it->getId(),
				array (
						'Password' => $new_password 
				)
		);
		
	    $language = $user_it->get('Language') == 1 ? 'ru' : 'en';

	    $body = preg_replace('/\%key\%/', $new_password, 
	    		file_get_contents(SERVER_ROOT_PATH.'plugins/account/resources/'.$language.'/reset-password.html')
			);

	    $mail = new HtmlMailbox;
	    $mail->appendAddress($user_it->get('Email'));
	    $mail->setBody($body);
	    $mail->setSubject(text('account25'));
	    $mail->setFrom("Devprom Software <".getFactory()->getObject('cms_SystemSettings')->getAll()->get('AdminEmail').">");
	    $mail->send();
	}
}
