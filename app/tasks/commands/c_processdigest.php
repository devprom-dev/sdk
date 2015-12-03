<?php

include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';
include_once SERVER_ROOT_PATH.'pm/classes/notificators/EmailNotificator.php';
include_once SERVER_ROOT_PATH.'pm/classes/notificators/DigestHandler.php';

class ProcessDigest extends TaskCommand
{
 	function execute()
	{
		global $model_factory;

		$this->logStart();
		
		$job = $model_factory->getObject('co_ScheduledJob');

		$job_it = $job->getExact($_REQUEST['job']);
		$parameters = $job_it->getParameters();

		$notification_type = $parameters['type'] != '' 
			? $parameters['type'] : $job_it->get('Parameters');
			
		$batch_it = $this->getJob();

		if ( $batch_it->count() > 0 && $notification_type == '' )
		{
			while ( !$batch_it->end() )
			{
		 		if ( function_exists('json_decode') )
		 		{
		 			$batch_parms = json_decode( 
		 				$batch_it->getHtmlDecoded('Parameters'), true );
		 		}
		 		else
		 		{
		 			$batch_parms = array( 
		 				'items' => $batch_it->getHtmlDecoded('Parameters')
		 			);
		 		}
				
		 		$this->logBatchParms( $batch_it->getHtmlDecoded('Parameters') );
				
		 		$batch_it->delete();
				
		 		$this->processChunk( 
					preg_split('/,/', $batch_parms['items']),
					$batch_parms['date'], 
					$batch_parms['length'],
					$batch_it->get_native('RecordCreated') 
				);
					
				$batch_it->moveNext();
			}
		}
		else if ( $notification_type != '' )
		{
		 	$this->logParms( $parameters );
			
			$log_items = $parameters['limit'] > 0 ? $parameters['limit'] : 0;
			$step = $parameters['step'] > 0 ? $parameters['step'] : 100;
			
			$notification = $model_factory->getObject('Notification');
			
			// get participants to be notified
			$recipient_it = $notification->getParticipantIt( $notification_type );

			switch ( $notification_type )
			{
				case 'every10minutes':
					$offset = '-10 minute';
					break;
					
				case 'every1hour':
					$offset = '-1 hour';
					break;
				
				case 'daily':
					$offset = '-1 day';
					break;
					
				case 'every2days':
					$offset = '-2 day';
					break;
					
				case 'weekly':
					$offset = '-1 week';
					break;
					
				default:
					return;
			}

			$batch_parms = array(
				'date' => date('Y-m-d H:i', strtotime($offset, strtotime(SystemDateTime::date('Y-m-d H:i')))),
				'length' => $log_items
			);

			$chunks = array_chunk($recipient_it->idsToArray(), $step);
			
			$immediate_chunk = array_shift( $chunks );
			
			foreach ( $chunks as $chunk )
			{
				$batch_parms['items'] = join(',', $chunk);
				
		 		$this->logBatchParms( $batch_parms['items'] );
				
		 		$this->addJob(JsonWrapper::encode($batch_parms));
			}

			$this->processChunk( 
					$immediate_chunk, 
					$batch_parms['date'], 
					$batch_parms['length'], 
					SystemDateTime::date('Y-m-d H:i') 
			);
		}
		
		$this->logFinish();
	}
	
	function processChunk( $chunk, $from_date = '', $log_items = 0, $till_date = '' )
	{
		global $model_factory, $session;

		$auth_factory = new AuthenticationFactory();
			
		$auth_factory->setUser( $model_factory->getObject('cms_User')->getEmptyIterator() );
		
		$logger = $this->getLogger();
		
		// get sender address
		$settings = $model_factory->getObject('cms_SystemSettings');
		$settings_it = $settings->getAll();
	
		$log = $model_factory->getObject('ChangeLogAggregated');

		$project = $model_factory->getObject('pm_Project');
		
		$from_date = $from_date != '' ? $from_date : '3011-01-01';

		// prepare and send email to each participant
		$recipient_it = getFactory()->getObject('pm_Participant')->getRegistry()->Query(
				array( new FilterInPredicate( $chunk ) )
		);
		
		$notificator = new EmailNotificator();

		while ( !$recipient_it->end() )
		{
			if ( is_object($logger) )
			{
				$logger->info( 
					str_replace('%2', $recipient_it->get('Project'),
						str_replace('%1', $recipient_it->getId(), "Processing participant [%1] on project [%2]" )
					)
				);
			}
			
	   		if ( $recipient_it->get('Project') == '' )
	   		{
				if ( is_object($logger) )
				{
		   			$logger->info( 
						str_replace('%1', $recipient_it->getId(), "Project is null for participant [%1]" )
					);
				}
				
	   			$recipient_it->moveNext();
				continue;
	   		}

			$project_it = $project->getExact($recipient_it->get('Project'));
	   		
	   		if ( $project_it->getId() < 1 )
	   		{
				if ( is_object($logger) )
				{
		   			$logger->info( 
						str_replace('%1', $recipient_it->get('Project'), "Project not found [%1]" )
					);
				}
	   			
				$recipient_it->moveNext();
				continue;
	   		}
	   		
	   		if ( !$project_it->IsActive() )
	   		{
				if ( is_object($logger) )
				{
		   			$logger->info( 
						str_replace('%1', $project_it->get('CodeName'), "Project is closed [%1]" )
					);
				}
	   			
				$recipient_it->moveNext();
				continue;
	   		}

			$registry = getFactory()->getObject('pm_ProjectUse')->getRegistry();
			
			$registry->setLimit(1);
			
			$tz = $registry->Query(
					array (
						new FilterAttributePredicate('Participant', $recipient_it->get('SystemUser')),
						new SortRecentClause()
					)
				)->get('Timezone');
					
			$logger->info('Recipient time zone: '.$tz);					

			EnvironmentSettings::setClientTimeZone($tz);
	   		
			$till_date_client = SystemDateTime::convertToClientTime($till_date);
			$from_date_client = SystemDateTime::convertToClientTime($from_date);
			
			if ( is_object($logger) )
			{
				$logger->info(
					str_replace('%2', $till_date_client,
						str_replace('%1', $from_date_client, 
							str_replace('%0', $project_it->get('CodeName'), 
								"Get log changes on [%0] since [%1] till [%2] "
							)
						)
					) 
				);
			}
			
			$session = new PMSession($project_it->copy(), $auth_factory);
			
			$log_registry = $log->getRegistry();
			
			if ( $log_items > 0 ) $log_registry->setLimit( $log_items );

			$log_it = $log_registry->Query( 
					array(
							new ChangeLogVisibilityFilter(),
							// get log items related to the session's project
							new FilterBaseVpdPredicate(),
							// do not append log items made by the participant to be notified
							new ChangeLogExceptParticipantFilter( $recipient_it->getId() ),
							// do not append log items made by the user to be notified
							new ChangeLogExceptUserFilter( $recipient_it->get('SystemUser') ),
							// setup left timeline offset
							new ChangeLogStartFilter( $from_date_client ),
							// setup right timeline offset
							new ChangeLogFinishFilter( $till_date_client ),
							// sort by recent records
							new SortChangeLogRecentClause()
						)
			);

			if ( is_object($logger) )
			{
				$logger->info( str_replace('%1', $recipient_it->getDisplayName().' ['.$recipient_it->get('Email').']', 
					str_replace('%2', $project_it->get('CodeName'), 
						str_replace('%3', $log_it->getId() > 0 ? $log_it->count() : 0, text(1211)))) );
			}
	   		
	   		if ( $log_it->getId() < 1 )
	   		{
	   			$recipient_it->moveNext();
	   			continue;
	   		}

	   		$handler = $notificator->getHandler($log_it);
	   		$handler->setFromDate($from_date_client);
	   		$handler->setRecipient($recipient_it);
	   		
	   		$queues = $notificator->sendMail('', $log_it, $log_it);
	   		
			if ( is_object($logger) ) $logger->info( str_replace('%1', array_pop($queues), text(1212)) );
	
			$recipient_it->moveNext();
		}
	}
}