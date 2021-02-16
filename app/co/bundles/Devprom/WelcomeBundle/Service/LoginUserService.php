<?php

namespace Devprom\WelcomeBundle\Service;

define ('MAX_PASSWORD_RETRIES', 10);

class LoginUserService
{
    const WRONG_PASSWORD = 2;
 	var $user_it;
 	
 	function validate( $login = '', $password = '', $email = '')
 	{
		$login = $login == '' ? $_REQUEST['login'] : $login;
		$pass = $password == '' ? $_REQUEST['pass'] : $password;
		
		if( trim($login) == '' || trim($pass) == '' ) return 1;

		$cls_user = getFactory()->getObject('cms_User');
		$password_hash = $cls_user->getHashedPassword( $pass );
		
	    $this->user_it = $cls_user->getByRefArray(
			array( 'LCASE(Login)' => trim(strtolower($login)),
				   'Password' => $password_hash ) 
			);
        if ( $this->user_it->count() < 1 ) {
            $this->user_it = $cls_user->getByRefArray( array(
                    'LCASE(Email)' => trim(strtolower($login)),
                    'Password' => $password_hash
                ));
        }
		if ( $this->user_it->count() < 1 )
		{
		    $this->user_it = $cls_user->getByRefArray(
				array( 'LCASE(Login)' => strtolower(trim($login)) ) 
				);
            if ( $this->user_it->count() < 1 ) {
                $this->user_it = $cls_user->getByRefArray(
                    array( 'LCASE(Email)' => strtolower(trim($login)) )
                );
            }

			if ( $this->user_it->count() > 0 )
			{
				$result = $this->validateUser($this->user_it);
				if ( $result > 0 ) return $result;

				if ( $this->user_it->get('Password') == '' ) return 5;

				// check if this is an attack on password
				$retry = new \Metaobject('cms_LoginRetry');
				$retry_it = $retry->getByRef('SystemUser', $this->user_it->getId());

				$maxRetries = MAX_PASSWORD_RETRIES;
				if ( $this->user_it->get('IsAdmin') == 'Y' ) {
                    $maxRetries = $maxRetries * 5;
                }

				if ( $retry_it->count() > 0 )
				{
					if ( $retry_it->get('RetryAmount') < $maxRetries + 1 )
					{
						if ( $retry_it->get('RetryAmount') >= $maxRetries )
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

							$log = new \Metaobject("ObjectChangeLog");
                            $log->add_parms(
                                array(
                                    'Caption' => $this->user_it->getDisplayName(),
                                    'ObjectId' => $this->user_it->getId(),
                                    'ClassName' => 'user',
                                    'EntityRefName' => 'cms_User',
                                    'EntityName' => $this->user_it->object->getDisplayName(),
                                    'ChangeKind' => 'modified',
                                    'Content' => translate('Учетная запись пользователя заблокирована'). ': ' . text(1512)
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

			return self::WRONG_PASSWORD;
		}
		else
		{
			$result = $this->validateUser($this->user_it);
			if ( $result > 0 ) return $result;

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

	function validateUser( $user_it ) {
		if ( !is_object($user_it) ) return self::WRONG_PASSWORD;
		if ( $user_it->getId() < 1 ) return self::WRONG_PASSWORD;
		if ( $user_it->IsBlocked() ) return 4;
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
				
            case self::WRONG_PASSWORD:
				return str_replace('%1', MAX_PASSWORD_RETRIES, text(494));

			case 3:
				return text(226);

			case 4:
				return text(380);

            case 5:
                return text(2048);

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
   		$mail->setBody($body);
   		$mail->setSubject( text(227) );
		$mail->send();
	}
}
