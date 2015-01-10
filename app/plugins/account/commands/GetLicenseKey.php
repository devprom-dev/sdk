<?php
 
class GetLicenseKey extends CommandForm
{
 	function validate()
 	{
 		global $user_it, $_REQUEST;
 		
 		if ( !$user_it->IsReal() )
 		{
 			$this->replyError( 'Получить лицензию могут только зарегистрированные пользователи.' );
 		}
 		
		$this->checkRequired( array('InstallationUID', 'LicenseType') );
		
		if ( $_REQUEST['LicenseType'] != 'LicenseTeam' )
		{
			$this->checkRequired( array('LicenseValue') );
			
			if ( intval($_REQUEST['LicenseValue']) < 1 )
			{
            	switch( $_REQUEST['LicenseType'] )
            	{
            	    case 'LicenseSAASALM':
            	    case 'LicenseSAASALMMiddle':
            	    case 'LicenseSAASALMLarge':
						$this->replyError( 'Укажите количество месяцев использования Devprom.' );
						break;
						
            	    default:
            	    	$this->replyError( 'Укажите количество пользователей.' );
            	}
			}
		}

 		if ( in_array($_REQUEST['LicenseType'], array('LicenseSAASALM', 'LicenseSAASALMMiddle', 'LicenseSAASALMLarge')) )
		{
			if ( !in_array(strtolower($_REQUEST['Aggreement']), array('on', 'y')) )
			{
				$this->replyError( 'Для продолжения необходимо изучить и принять условия публичной оферты.' ); 
			}
		}
		
		return true;
 	}
 	
 	function create()
	{
		global $model_factory, $_REQUEST, $user_it;

		if ( in_array($_REQUEST['LicenseType'], array('LicenseSAASALM', 'LicenseSAASALMMiddle', 'LicenseSAASALMLarge')) && $_REQUEST['LicenseKey'] == '' )
		{
			$this->redirectToStore();
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
            	case 'LicenseSAASALM':
           	    case 'LicenseSAASALMMiddle':
           	    case 'LicenseSAASALMLarge':
				
            		$license_data['key'] = $_REQUEST['LicenseKey'];
					
					break;
					
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

			   		$mail = new HtmlMailbox;
			   		
			   		$from_address = 'Dmitry Lobasev <dmitry.lobasev@devprom.ru>'; 
			   		$subject = 'Re: Загрузка дистрибутива Devprom ALM';
		   			$body = 'Добрый день!<br/><br/>Поздравляю вас с успешной установкой Devprom ALM!<br/><br/>Хочу предложить провести для вас и ваших коллег небольшую демонстрацию возможностей Devprom ALM, чтобы вместе с вами лучше понять, как произвести настройку инструмента под потребности процессов вашей компании. Обычно это позволяет сэкономить достаточно много времени, которое бы вы потратили на самостоятельное изучение.<br/><br/>Мы можем провести демонстрацию в вашем офисе, если он в Москве, или через видео-звонок в skype.<br/><br/>Пожалуйста, напишите или позвоните мне, если это предложение вам интересно, мы выберем удобное время встречи.<br/><br/>Также приглашаем вас <a href="http://devprom.ru/news/tag/Вебинар">принять участие в нашем вебинаре</a>, где мы расскажем о возможностях Devprom, а вы сможете задать нам интересующие вас вопросы.<br/><br/>Дмитрий<br/>______________________________________<br/>Dmitry Lobasev, Managing Partner, Devprom<br/>tel: +7 (499) 638-64-11, skype: dmitry.lobasev<br/>web: <a href="http://devprom.ru/">http://devprom.ru</a><br/><br/>Нажмите <strong>Мне нравится</strong> на странице <a href="http://facebook.com/devprom">Devprom в Facebook</a> - будьте в курсе последних новостей о наших продуктах!';
			   		
			   		$mail->setBody(wordwrap($body, 70, "\n"));
			   		$mail->setSubject($subject);
			   		
			   		$headers = "From: ".$mail->encodeAddress($from_address)."\r\n";
    				$headers .= "Reply-To: ".$mail->encodeAddress($from_address)."\r\n";
    				$headers .= $mail->getContentType()."\r\n";
			   		
			   		//mail( $mail->encodeAddress($user_it->get('Email')), $mail->subject, $mail->getBody(), $headers, "-f dmitry.lobasev@devprom.ru");
					
					break;
					
				default:
					
					return $this->replyError('Данный вид лицензирования не поддерживается');
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
		
		if ( $license_data['type'] == 'LicenseTrial' )
		{
			$this->exportToCRM( $user_it, $license_data );
		}
		
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
	
	function delete()
	{
		global $_REQUEST;
		
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
	
	function redirectToStore()
	{
		$merchantId = 62021;
		$currency = "RUB";
		$securityKey = "30cfcab4-ce10-413f-bbfd-4a367823bc1c";

		switch( $_REQUEST['LicenseType'] )
		{
		    case 'LicenseSAASALM':
		    	
		    	$amount = round($_REQUEST['LicenseValue'] * 3000, 0); 
		    	
		    	break;
		    	
            case 'LicenseSAASALMMiddle':

            	$amount = round($_REQUEST['LicenseValue'] * 15000, 0); 
		    	
		    	break;
		    	
            default:
            	$amount = round($_REQUEST['LicenseValue'] * 60000, 0); 
		}
		
		$amount .= ".00"; 

		$orderId = abs(crc32($_REQUEST['InstallationUID'].date('Y-m-d H:s:i')));
		
		$baseQuery = "MerchantId=".$merchantId.
                     "&OrderId=".$orderId.
                     "&Amount=".$amount.	
                     "&Currency=".$currency;

		$queryWithSecurityKey = $baseQuery."&PrivateSecurityKey=".$securityKey;

		$hash = md5($queryWithSecurityKey);

		$clientQuery = $baseQuery."&SecurityKey=".$hash;

		$paymentFormAddress = "https://secure.payonlinesystem.com/ru/payment/?".$clientQuery;

		$order_info = array (
				'LicenseType' => $_REQUEST['LicenseType'],
				'LicenseValue' => $_REQUEST['LicenseValue'],
				'WasLicenseKey' => $_REQUEST['WasLicenseKey'],
				'WasLicenseValue' => $_REQUEST['WasLicenseValue'],
				'InstallationUID' => $_REQUEST['InstallationUID'],
				'Redirect' => $_REQUEST['Redirect'],
				'Amount' => $amount,
				'OrderId' => $orderId
		);
		
		setcookie('devprom-order-info', JsonWrapper::encode($order_info), 0, '/' );
		
		$this->replyRedirect($paymentFormAddress);
	}
}
