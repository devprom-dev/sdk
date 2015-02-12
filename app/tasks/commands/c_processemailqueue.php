<?php
 
 if ( !class_exists('MailBox') )
 {
 	include(dirname(__FILE__).'/../../cms/c_mail.php'); 
 }
 
 ////////////////////////////////////////////////////////////////////////////
 class ProcessEmailQueue extends TaskCommand
 {
 	function execute()
	{
		global $model_factory;
		
		$this->logStart();
		$logger = $this->getLogger();
		
		$settings = $model_factory->getObject('cms_SystemSettings');
		$settings_it = $settings->getAll();
		
		$user = $model_factory->getObject('cms_User');
		$queue = $model_factory->getObject('EmailQueue');
		$address = $model_factory->getObject('EmailQueueAddress');
		
		$job = $model_factory->getObject('co_ScheduledJob');
		$job_it = $job->getExact($_REQUEST['job']);
		$parameters = $job_it->getParameters();
		
		$process_items = $parameters['limit'] > 0 ? $parameters['limit'] : 10;  

		// выберем все очереди
		$queue_it = $queue->getLatest($process_items);
		while ( !$queue_it->end() )
		{
   			$body = $queue_it->getHtmlDecoded('Description');
	
			if ( $queue_it->get('MailboxClass') != '' )
			{
				$mailbox_class = $queue_it->get('MailboxClass');
				$mail = new $mailbox_class;
			}
			else
			{
				$mail = new MailBox;
			}
			
			if ( $settings_it->get('AdminEmail') != '' )
			{
				$email_match = array();
				
				if ( preg_match('/<([^>]+)>/', $settings_it->getHtmlDecoded('AdminEmail'), $email_match) )
				{
					$admin_address = $email_match[1];
				}
				else
				{
					$admin_address = $settings_it->getHtmlDecoded('AdminEmail');
				}
			}
			
			$from_address = $queue_it->getHtmlDecoded('FromAddress');
			
			// выберем подписчиков
			$address_it = $address->getByRef(
				'EmailQueue', $queue_it->getId());
				
			while ( !$address_it->end() ) 
			{
				$to_address = $address_it->getHtmlDecoded('ToAddress');
					
	   			$body = str_replace('<%EMAIL%>', $to_address, $body);

				if ( $address_it->get('cms_UserId') > 0 )
				{
					$user_it = $user->getExact($address_it->get('cms_UserId'));
		   			$body = str_replace('%USERNAME%', $user_it->getDisplayName(), $body);
				}

				if ( defined('EMAIL_SENDER_TYPE') && EMAIL_SENDER_TYPE == 'admin' )
				{ 
					$headers = 
						"Sender: ".$mail->encodeAddress(
							$mail->addressUpdateEmail($from_address, $admin_address))."\r\n";
					
					$headers .=
						"From: ".$mail->encodeAddress(
							$mail->addressUpdateEmail($from_address, $admin_address))."\r\n";
				}
				else
				{
					$headers = 
						"From: ".$mail->encodeAddress($from_address)."\r\n";
				}
				
				/*
				$headers .= 
					"Reply-To: ".$mail->encodeAddress($from_address)."\r\n";
				*/
				
				if ( is_object($mail) )
				{
					$headers .= $mail->getContentType()."\r\n"; 
				}

				$address->delete($address_it->getId());

				if ( $to_address != '' )
				{
					$transport = defined('EMAIL_TRANSPORT') 
						? (EMAIL_TRANSPORT == '1' ? 'SMTP' : 'IMAP') : 'SMTP';
					
					// to avoid php's mail limitations
					$body = wordwrap($body, 70, "\n");
						
					switch ( $transport )
					{
						case 'IMAP':
					        imap_mail( $mail->encodeAddress($to_address), 
					        	$queue_it->get('Caption'), $body, $headers );
					        	
							break;
							
						default:
							if ( $admin_address != '' )
							{
								$force = "-f ".$admin_address;
							}

							mail($mail->encodeAddress($to_address), 
								$queue_it->get('Caption'), $body, $headers, $force);
					}

					$logger->info( str_replace('%1', $queue_it->get('Caption'), 
						str_replace('%2', $to_address, 
							str_replace('%3', $transport, text(1213)))) );
				}
						
				$address_it->moveNext();
			}

			$address_it = $address->getByRef(
				'EmailQueue', $queue_it->getId());
				
			if ( $address_it->count() < 1)
			{
				$queue->delete($queue_it->getId());
			}

			$queue_it->moveNext();
		}
		
		$this->logFinish();
	}
 }
 
?>