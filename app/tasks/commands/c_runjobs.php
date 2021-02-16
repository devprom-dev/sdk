<?php
include_once SERVER_ROOT_PATH.'core/classes/system/GlobalLock.php';

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

		// recover table
		$this->repairTables();
        
		$job = getFactory()->getObject('co_ScheduledJob');
		$jobrun = getFactory()->getObject('co_JobRun');

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

		// execute jobs
		if ( count($jobs_to_run) > 0 )
		{
			$job_it = $job->getInArray('co_ScheduledJobId', $jobs_to_run);
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
						
						getFactory()->getObject('co_JobRun')->add_parms( array ( 
							'ScheduledJob' => $job_it->getId(),
							'Result' => $result,
							'IsCompleted' => 'Y',
							'RecordCreated' => $started_date 
						));
					}
					catch( Exception $e )
					{
						core\classes\ExceptionHandler::Instance()->captureException($e);
						if ( is_object($this->getLogger()) ) {
							$this->getLogger()->error( get_class($command).': '.$e->getMessage() );
						}
					}
					
					// remove old results
					while ( true )
					{
						$cnt = $jobrun->getRegistry()->Count(
						    array(
						        new FilterAttributePredicate('ScheduledJob', $job_it->getId())
                            )
                        );
						if ( $cnt < 21 ) break;

						$run_it = $jobrun->getRegistry()->Query(
						    array(
                                new FilterAttributePredicate('ScheduledJob', $job_it->getId()),
                                new SortAttributeClause('RecordCreated')
                            )
                        );
						if ( $run_it->delete() < 1 ) break;
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
		if ( $pattern == '*' )
		{
			return true;	
		}
		
		$period = preg_split('/\//', $pattern);
		if ( count($period) > 1 )
		{
			$checkpoints = array();
			for ( $i = 0; $i < 100; $i += $period[1] )
			{
				array_push($checkpoints, $i);
			}
			return in_array( $value, $checkpoints );
		}

		$interval = preg_split('/-/', $pattern);
		if ( count($interval) > 1 )
		{
			if ( $value >= $interval[0] && $value <= $interval[1] )
			{
				return true;
			}
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
}
