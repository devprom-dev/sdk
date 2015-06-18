<?php

namespace Devprom\ApplicationBundle\Service;

define ('MAX_PASSWORD_RETRIES', 10);

class LoginUserService
{
 	var $user_it;
 	
 	function validate( $login = '', $password = '', $email = '')
 	{
		$login = \IteratorBase::utf8towin($login == '' ? $_REQUEST['login'] : $login);

		$pass = \IteratorBase::utf8towin($password == '' ? $_REQUEST['pass'] : $password);
		
		if( $login == '' || $pass == '' ) 
		{
			return 1;
		}
		
		$cls_user = getFactory()->getObject('cms_User');
		
		$password_hash = $cls_user->getHashedPassword( $pass );
		
	    $this->user_it = $cls_user->getByRefArray(
			array( 'LCASE(Login)' => trim(strtolower($login)),
				   'Password' => $password_hash ) 
			);
		
		if ( $this->user_it->count() < 1 )
		{
			// check if this is an attack on password 
		    $this->user_it = $cls_user->getByRefArray(
				array( 'LCASE(Login)' => strtolower(trim($login)) ) 
				);
				
			if ( $this->user_it->count() > 0 )
			{
				$retry = new \Metaobject('cms_LoginRetry');
				$retry_it = $retry->getByRef('SystemUser', $this->user_it->getId());
				
				if ( $retry_it->count() > 0 )
				{
					if ( $retry_it->get('RetryAmount') < MAX_PASSWORD_RETRIES + 1 )
					{
						if ( $retry_it->get('RetryAmount') >= MAX_PASSWORD_RETRIES )
						{
							// add ip address into black list
							$list = new \Metaobject('cms_BlackList');
							
							$list->add_parms(
								array (
									'SystemUser' => $this->user_it->getId(),
									'IPAddress' => $_SERVER['REMOTE_ADDR'],
									'BlockReason' => text(1512)
									)
								);
								
							$this->sendRetryNotification( $this->user_it, $_SERVER['REMOTE_ADDR'] );
						}
						
						$retry->modify_parms($retry_it->getId(), array('RetryAmount' => $retry_it->get('RetryAmount') + 1 ) );
					}
					else
					{
						return 4;
					}
				}
				else
				{
					$retry->add_parms( array('SystemUser' => $this->user_it->getId()) );
				}
			}
				
			return 2;
		}
		else
		{
			if ( $this->user_it->IsBlocked() )
			{
				return 4;
			}
			
			$retry = new \Metaobject('cms_LoginRetry');
			$retry_it = $retry->getByRef('SystemUser', $this->user_it->getId());
			
			if ( $retry_it->count() > 0 )
			{
				$retry->delete($retry_it->getId());
			}
		}
		
		$sql = " SELECT COUNT(1) cnt FROM cms_BlackList WHERE SystemUser = ".$this->user_it->getId();
		$it =  $this->user_it->object->createSQLIterator($sql);
		
		if ( $it->get('cnt') > 0 )
		{
			return 3;
		}

 		return 0;
 	}
 	
 	function getUserIt()
 	{
 		return $this->user_it;
 	}
 	
	function getResultDescription( $result )
	{
		switch($result)
		{
			case -1:
				return 'granted';

			case 1:
				return text(224);
				
			case 2:
				return str_replace('%1', MAX_PASSWORD_RETRIES, text(494));

			case 3:
				return text(226);

			case 4:
				return text(380);

			default:
				return $result;
		}
	}
	
	function sendRetryNotification( $user_it, $ip_address )
	{
 		$settings_it = getFactory()->getObject('cms_SystemSettings')->getAll();

		$message = translate('Здравствуйте, %s!').Chr(10).Chr(10);
		$message = str_replace('%s', $user_it->getDisplayName(), $message);
		
		$body = $message.text(59);
		$body = str_replace('%1', $ip_address, $body);
		$body = str_replace('%2', $settings_it->getHtmlDecoded('AdminEmail'), $body);

   		$mail = new \Mailbox;
   		$mail->appendAddress($user_it->get('Email'));
   		$mail->appendAddress($settings_it->getHtmlDecoded('AdminEmail'));
   		$mail->setBody($body);
   		$mail->setSubject( text(227) );
   		$mail->setFrom($settings_it->getHtmlDecoded('AdminEmail'));
		$mail->send();
	}
}
