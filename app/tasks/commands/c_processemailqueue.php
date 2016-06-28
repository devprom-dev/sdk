<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

include_once SERVER_ROOT_PATH.'cms/c_mail.php';
use Devprom\ApplicationBundle\Service\Mailer\DevpromSwiftMessage;

class ProcessEmailQueue extends TaskCommand
{
 	function execute()
	{
		global $kernel;

		$this->logStart();

		getFactory()->setAccessPolicy( new \AccessPolicy() );
		$mailer = $kernel->getContainer()->get('mailer');

		$user = getFactory()->getObject('cms_User');
		$queue = getFactory()->getObject('EmailQueue');
		$address = getFactory()->getObject('EmailQueueAddress');

		$job = getFactory()->getObject('co_ScheduledJob');
		$job_it = $job->getExact($_REQUEST['job']);
		$parameters = $job_it->getParameters();
		$process_items = $parameters['limit'] > 0 ? $parameters['limit'] : 10;

		$queue_it = $queue->getLatest($process_items);
		$this->getLogger()->info('Emails to be processed: '.$queue_it->count());

		while ( !$queue_it->end() )
		{
			$body = $queue_it->getHtmlDecoded('Description');
			$from_address = $queue_it->getHtmlDecoded('FromAddress');

			$address_it = $address->getByRef('EmailQueue', $queue_it->getId());
			while ( !$address_it->end() )
			{
				$to_address = $address_it->getHtmlDecoded('ToAddress');

	   			$body = str_replace('<%EMAIL%>', $to_address, $body);
				if ( $address_it->get('cms_UserId') > 0 ) {
					$user_it = $user->getExact($address_it->get('cms_UserId'));
		   			$body = str_replace('%USERNAME%', $user_it->getDisplayName(), $body);
				}

				$address->delete($address_it->getId());

				list($from_email, $from_name) = HtmlMailBox::parseAddressString($from_address);
				list($to_email, $to_name) = HtmlMailBox::parseAddressString($to_address);

				try {
					if ( $from_email == $to_email ) throw new \Exception("skip sending to itself ".$from_email);

					$mailer->send(
						DevpromSwiftMessage::newInstance()
							->setContentType(HtmlMailBox::getContentType())
							->setFrom($from_email, $from_name != '' ? $from_name : null)
							->setSender($from_email, $from_name != '' ? $from_name : null)
							->setTo($to_email, $to_name != '' ? $to_name : null)
							->setSubject($queue_it->getHtmlDecoded('Caption'))
							->setBodyNative($body)
					);
				}
				catch (\Exception $e) {
					$this->getLogger()->error("Unable send email: ".$e->getMessage().PHP_EOL.$e->getTraceAsString());
				}

				$address_it->moveNext();
			}

			$queue->delete($queue_it->getId());
			$queue_it->moveNext();
		}

		$this->logFinish();
	}
}
