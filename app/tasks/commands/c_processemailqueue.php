<?php
include_once SERVER_ROOT_PATH.'cms/c_mail.php';

class ProcessEmailQueue extends TaskCommand
{
 	function execute()
	{
		global $model_factory;
		
		$this->logStart();
		$logger = $this->getLogger();
		
		$user = $model_factory->getObject('cms_User');
		$queue = $model_factory->getObject('EmailQueue');
		$address = $model_factory->getObject('EmailQueueAddress');
		
		$job = $model_factory->getObject('co_ScheduledJob');
		$job_it = $job->getExact($_REQUEST['job']);
		$parameters = $job_it->getParameters();
		
		$process_items = $parameters['limit'] > 0 ? $parameters['limit'] : 10;
		$admin_address = HtmlMailBox::getSystemEmail();

		$queue_it = $queue->getLatest($process_items);
		while ( !$queue_it->end() )
		{
			$body = $queue_it->getHtmlDecoded('Description');
			$from_address = $queue_it->getHtmlDecoded('FromAddress');
			
			$address_it = $address->getByRef('EmailQueue', $queue_it->getId());
			while ( !$address_it->end() )
			{
				$to_address = $address_it->getHtmlDecoded('ToAddress');
					
	   			$body = str_replace('<%EMAIL%>', $to_address, $body);

				if ( $address_it->get('cms_UserId') > 0 )
				{
					$user_it = $user->getExact($address_it->get('cms_UserId'));
		   			$body = str_replace('%USERNAME%', $user_it->getDisplayName(), $body);
				}

				$headers = "Sender: ".HtmlMailBox::encodeAddress($from_address)."\r\n";
				$headers .= "From: ".HtmlMailBox::encodeAddress($from_address)."\r\n";
				$headers .= HtmlMailBox::getContentType()."\r\n";

				$address->delete($address_it->getId());

				if ( $to_address != '' )
				{
					if ( $admin_address != '' ) $force = "-f ".$admin_address;

					mail(HtmlMailBox::encodeAddress($to_address), $queue_it->get('Caption'), $body, $headers, $force);

					$logger->info(
						str_replace('%1', $queue_it->get('Caption'),
							str_replace('%2', $to_address,
								str_replace('%3', 'SMTP', text(1213)))) );
				}
						
				$address_it->moveNext();
			}

			$address_it = $address->getByRef('EmailQueue', $queue_it->getId());
			if ( $address_it->count() < 1) $queue->delete($queue_it->getId());

			$queue_it->moveNext();
		}
		
		$this->logFinish();
	}
}
