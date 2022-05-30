<?php
include_once SERVER_ROOT_PATH.'core/classes/system/GlobalLock.php';
include_once SERVER_ROOT_PATH.'pm/classes/sessions/PMSession.php';

class RunJobs extends Command
{
	private $timeWaitedForPrevInstance = 300;

    function IsAuthenticationRequired() {
        return false;
    }

 	function execute()
	{
		global $model_factory, $plugins, $_REQUEST, $_SERVER;

        $this->timeWaitedForPrevInstance = defined('BACKGROUND_JOB_WAITTIME') ? BACKGROUND_JOB_WAITTIME : 300;

		$maintainLock = new LockFileSystem(MAINTENANCE_LOCK_NAME);
		if ( $maintainLock->Locked($this->timeWaitedForPrevInstance) ) {
			if ( is_object($this->getLogger()) ) {
				$this->getLogger()->info('Maintenance is in progress, abort');
			}
			return;
		}
        $maintainLock = new GlobalLock();

		$this->logStart();

        $jobs_to_run = array();

        // see SessionBuilder for more details
        \FileSystem::rmdirr( SERVER_FILES_PATH . 'sessions' );

		$job = getFactory()->getObject('co_ScheduledJob');

		// select jobs to be executed
		if ( $_REQUEST['job'] > 0 )
		{
			$job_it = $job->getExact($_REQUEST['job']);
			
			if ( $job_it->count() > 0 )
			{
				array_push ( $jobs_to_run, $job_it->getId() );
				$job_it = $job->getExact(0);
			}
		}
		else
		{
			$job_it = $job->getRegistry()->Query(
                array (
                    new FilterAttributePredicate('ClassName', preg_split('/,/',$_REQUEST['filter'])),
                    new SortOrderedClause()
                )
			);
		}

		// store the time the command was executed last time
		$info_path = DOCUMENT_ROOT.'conf/runtime.info';

		$file = fopen( $info_path, 'w', 1 );
		fwrite( $file, time() );
		fclose( $file );
		
		// execute jobs
		while ( !$job_it->end() )
		{
			// check for active jobs
			if ( $job_it->get('IsActive') != 'Y' )
			{
				$job_it->moveNext();
				continue;		
			}
			
			// check for schedule
			$runjob = $this->checkForPattern( SystemDateTime::date('i'), trim($job_it->get('Minutes'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( SystemDateTime::date('H'), trim($job_it->get('Hours'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( SystemDateTime::date('j'), trim($job_it->get('Days'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( SystemDateTime::date('w'), trim($job_it->get('WeekDays'), ' '.chr(10).chr(13)) );
			
			if ( $runjob ) {
				array_push($jobs_to_run, $job_it->getId() );
			}
			
			$job_it->moveNext();		
		}

        $this->buildSearchableTexts();
        $this->execRecurrentActions();
        $this->execWebhooks();

		// execute jobs
		if ( count($jobs_to_run) > 0 )
		{
			$job_it = $job->getRegistry()->Query(array(
                    new FilterInPredicate($jobs_to_run)
                ));

			while ( !$job_it->end() )
			{
				$model_factory = new ModelFactoryExtended($plugins);
				SessionBuilderCommon::Instance()->openSession();

				$_REQUEST['job'] = $job_it->getId();
				 
 				$command = '';
 				$classname = $job_it->get('ClassName');
 				$include = dirname(__FILE__).'/../commands/c_'.$classname.'.php';

				if ( !class_exists($classname) && file_exists($include) )
				{
 					include( $include );
				}
 				
				if ( class_exists($classname) ) {
				 	$command = new $classname;	
				}
				else {
					$parts = preg_split('/\//', $classname);
					if ( count($parts) > 1 ) {
				 		$command = $plugins->getCommand( $parts[0], 'co', $parts[1] );
					}
				}
				 
				if ( is_object($command) )
				{
					$started_date = SystemDateTime::date();

					try
					{
                        $this->setupLogger(strtolower(get_class($command)));

						// pass concrete chunk to be processed
						if ( $_REQUEST['chunk'] != '' ) $command->setChunk(TextUtils::parseIds($_REQUEST['chunk']));

                        $jobLock = new LockFileSystem($classname);
                        if ( !$jobLock->Locked(defined('JOB_LOCK_TIMEOUT') ? JOB_LOCK_TIMEOUT : 1800) ) {
                            $jobLock->Lock();
                            $command->execute();
                            $jobLock->Release();
                            $result = translate('Выполнено');

                        }
                        else {
                            $result = translate('В ожидании');
                        }

						if ( is_object($this->getLogger()) ) $this->getLogger()->info(
                            $job_it->getDisplayName().': '.$result.': '.SystemDateTime::date()
                        );

                        $jobRunRegistry = getFactory()->getObject('co_JobRun')->getRegistry();
                        $jobRunRegistry->Create( array (
							'ScheduledJob' => $job_it->getId(),
							'Result' => $result,
							'IsCompleted' => 'Y',
							'RecordCreated' => $started_date 
						));

                        $jobRunRegistry->setLimit(100);
                        $jobRunRegistry->setOffset(20);
                        $oldItemIt = $jobRunRegistry->Query(
                            array(
                                new FilterAttributePredicate('ScheduledJob', $job_it->getId()),
                                new SortAttributeClause('RecordCreated.D')
                            )
                        );
                        while( !$oldItemIt->end() ) {
                            $jobRunRegistry->Delete($oldItemIt);
                            $oldItemIt->moveNext();
                        }
					}
					catch( Exception $e )
					{
						core\classes\ExceptionHandler::Instance()->captureException($e);
						if ( is_object($this->getLogger()) ) {
							$this->getLogger()->error( get_class($command).': '.$e->getMessage() );
						}

						$this->repairTables();
					}
				}
				
				$job_it->moveNext();
			}
		}

		$this->logFinish();
		$maintainLock->Release();

		if ( $_REQUEST['redirect'] != '' ) {
		    exit(header('Location: ' . $_REQUEST['redirect']));
        }
	}
	
	function checkForPattern( $value, $pattern )
	{
		if ( $pattern == '*' ) return true;

		$period = preg_split('/\//', $pattern);
		if ( count($period) > 1 )
		{
			$checkpoints = array();
			for ( $i = 0; $i < 100; $i += $period[1] ) {
				array_push($checkpoints, $i);
			}
			return in_array( $value, $checkpoints );
		}

		$interval = preg_split('/-/', $pattern);
		if ( count($interval) > 1 ) {
			if ( $value >= $interval[0] && $value <= $interval[1] ) {
				return true;
			}
		}

        $items = explode(',', $pattern);
        if ( count($items) > 1 ) {
            return in_array($value, $items);
        }

		return $pattern == $value;
	}
	
	function repairTables()
	{
        $it = getFactory()->getObject('cms_SystemSettings')->createSQLIterator(
            "show table status where Comment like '%crashed%' and Name = 'co_JobRun'"
        );
        if ( $it->count() < 1 ) return;

        DAL::Instance()->Query("check table co_JobRun");
	    DAL::Instance()->Query("repair table co_JobRun USE_FRM ");
	}

	function execRecurrentActions()
    {
        global $session;

        $this->setupLogger('recurrentactions');

        $auth_factory = new AuthenticationFactory();
        $auth_factory->setUser( getFactory()->getObject('cms_User')->getSuperUserIt() );

        $projectRegistry = getFactory()->getObject('Project')->getRegistry();
        $recurringRegistry = getFactory()->getObject('Recurring')->getRegistry();

        $projectIt = $projectRegistry->Query(
            array(
                new ProjectStatePredicate('active'),
                new FilterVpdPredicate(
                    $recurringRegistry->Query()->fieldToArray('VPD'))
            )
        );

        $log = $this->getLogger();

        if ( $projectIt->count() > 0 ) {
            if ( is_object($log) ) {
                $log->info( text(3102) );
            }
        }

        while( !$projectIt->end() )
        {
            $session = new PMSession($projectIt->copy(), $auth_factory);
            getFactory()->setAccessPolicy(new AccessPolicy(getFactory()->getCacheService()));
            getFactory()->resetCache();

            $recurringRegistry = getFactory()->getObject('Recurring')->getRegistry();
            $recurringIt = $recurringRegistry->Query(
                array(
                    new FilterAttributePredicate('IsActive', 'Y'),
                    new FilterVpdPredicate($projectIt->get('VPD'))
                )
            );
            $attributes = $recurringIt->object->getAttributesByGroup('recurring');
            while( !$recurringIt->end() )
            {
                if ( is_object($log) ) {
                    $log->info($recurringIt->getDisplayName());
                }

                list($minutes, $hours, $days, $dow, $months) = explode(' ', $recurringIt->get('CronSchedule'));
                $doExec = $this->checkForPattern( SystemDateTime::date('i'), $minutes) &&
                    $this->checkForPattern( SystemDateTime::date('H'), $hours) &&
                    $this->checkForPattern( SystemDateTime::date('j'), $days) &&
                    $this->checkForPattern( SystemDateTime::date('w'), $dow) &&
                    $this->checkForPattern( SystemDateTime::date('n'), $months);

                if ( !$doExec ) {
                    \Logger::getLogger('Commands')->info('Not now.');
                    $recurringIt->moveNext();
                    continue;
                }

                foreach( $attributes as $attribute ) {
                    if ( $recurringIt->get($attribute) == '' ) continue;
                    $objectIt = $recurringIt->getRef($attribute);
                    while( !$objectIt->end() ) {
                        try {
                            $objectIt->object->processRecurringAction($objectIt, $log);
                            if ( is_object($log) ) {
                                $log->info( sprintf(text(3103), $recurringIt->getDisplayName() . ' - ' . $objectIt->getDisplayName()) );
                            }
                        }
                        catch( \Exception $e ) {
                            if ( is_object($log) ) {
                                $log->error($e->getMessage());
                            }
                        }
                        $objectIt->moveNext();
                    }
                }
                $recurringIt->moveNext();
            }
            $projectIt->moveNext();
        }
    }

    function execWebhooks()
    {
        $this->setupLogger('webhooks');
        $log = $this->getLogger();

        $hookIt = getFactory()->getObject('co_WebhookLog')->getRegistry()->Query(
            array(
                new FilterAttributeGreaterPredicate('RetriesLeft', 0),
                new SortKeyClause()
            )
        );

        while( !$hookIt->end() )
        {
            $curl = CurlBuilder::getCurl();
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_REFERER, EnvironmentSettings::getServerUrl());
            curl_setopt($curl, CURLOPT_URL, $hookIt->getHtmlDecoded('Caption'));
            curl_setopt($curl, CURLOPT_HTTPHEADER,
                preg_split('/[\r\n]+/i', $hookIt->getHtmlDecoded('Headers')));

            switch($hookIt->get('Method')) {
                case 'GET':
                    curl_setopt($curl, CURLOPT_HTTPGET, true);
                    break;
                case 'POST':
                    curl_setopt($curl, CURLOPT_POST, true);
                    break;
                default:
                    curl_setopt($curl, CURLOPT_HTTPGET, false);
                    curl_setopt($curl, CURLOPT_POST, false);
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $hookIt->get('Method'));
            }

            curl_setopt($curl, CURLOPT_POSTFIELDS, $hookIt->getHtmlDecoded('Payload'));

            try {
                if ( is_object($log) ) {
                    $log->info($hookIt->getHtmlDecoded('Caption'));
                }

                $result = curl_exec($curl);
                if ( $result === false ) throw new \Exception(curl_error($curl));
                if ( is_object($log) ) {
                    $log->info(var_export($result,true));
                }

                $info = curl_getinfo($this->curl);
                if ( is_object($log) ) {
                    $log->info(var_export($info,true));
                }

                if ( $info['http_code'] >= 300 ) {
                    throw new Exception(var_export($info, true));
                }

                $hookIt->object->modify_parms($hookIt->getId(), array(
                    'RetriesLeft' => 0,
                    'Result' => $result
                ));
            }
            catch( \Exception $e ) {
                if ( is_object($log) ) {
                    $log->error($e->getMessage());
                }
                $hookIt->object->modify_parms($hookIt->getId(), array(
                    'RetriesLeft' => max(0, $hookIt->get('RetriesLeft') - 1),
                    'Result' => $e->getMessage()
                ));
            }

            curl_close($curl);

            $hookIt->moveNext();
        }
    }

    protected function buildSearchableTexts()
    {
        $registry = getFactory()->getObject('Searchable')->getRegistry();
        $registry->setLimit(100);
        $it = $registry->Query(
            array(
                new StaleSearchableFilter(),
                new SortRecentClause()
            )
        );

        while( !$it->end() ) {
            $className = getFactory()->getClass($it->get('ObjectClass'));
            if ( !class_exists($className) ) {
                $registry->Delete($it);
                $it->moveNext();
                continue;
            }

            $objectRegistry = getFactory()->getObject($className)->getRegistryBase();
            if ( $objectRegistry->getObject() instanceof \WikiPage ) {
                $objectRegistry = new \WikiPageRegistryContent($objectRegistry->getObject());
            }

            $objectIt = $objectRegistry->Query(
                array(
                    new FilterInPredicate($it->get('ObjectId'))
                )
            );
            if ( $objectIt->getId() == '' ) {
                $registry->Delete($it);
                $it->moveNext();
                continue;
            }

            $texts = array();
            $attributes = $objectIt->object->getAttributesByType('wysiwyg');
            foreach( $attributes as $attribute ) {
                $texts[] = \TextUtils::getNormalizedString($objectIt->getHtmlDecoded($attribute));
            }

            $registry->Store($it, array(
                'SearchContent' => join(' ', $texts),
                'IsActive' => 'Y'
            ));

            $it->moveNext();
        }
    }

    protected function setupLogger( $command )
    {
        $layout = new LoggerLayoutPattern();
        $layout->setConversionPattern("\n%d %l %n %m");
        $layout->activateOptions();

        $logFilePath = SERVER_LOGS_PATH . '/' . \TextUtils::getFileSafeString('task-' . $command . '.log');

        $appFile = new LoggerAppenderRollingFile('foo');
        $appFile->setFile($logFilePath);
        $appFile->setLayout($layout);
        $appFile->setAppend(true);
        $appFile->setThreshold('info');
        $appFile->activateOptions();

        $logger = Logger::getLogger('Commands');
        $logger->removeAllAppenders();
        $logger->addAppender($appFile);
        $logger->setLevel('info');
    }
}
