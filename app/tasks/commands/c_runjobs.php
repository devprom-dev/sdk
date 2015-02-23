<?php

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class RunJobs extends Command
{
 	function execute()
	{
		global $model_factory, $plugins, $_REQUEST, $_SERVER;

		$lock = new LockFileSystem(BACKGROUND_TASKS_LOCK_NAME);

		// single background job should be running at the same time 
		if ( $lock->Locked(60) )
		{
		    if ( is_object($this->getLogger()) )
		    {
				$this->getLogger()->info( 'Another instance of background job is running at the moment' );
		    }
		    
		    return;
		}

		// mark the background task is running
		$lock->Lock();
		
		$this->logStart();
		
		$jobs_to_run = array();

		// recover table
		$this->repairTables();
        
		$job = $model_factory->getObject('co_ScheduledJob');
		
		$jobrun = $model_factory->getObject('co_JobRun');

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
			$runjob = $this->checkForPattern( date('i'), trim($job_it->get('Minutes'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( date('H'), trim($job_it->get('Hours'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( date('j'), trim($job_it->get('Days'), ' '.chr(10).chr(13)) ) &&
				$this->checkForPattern( date('w'), trim($job_it->get('WeekDays'), ' '.chr(10).chr(13)) );
			
			if ( $runjob )
			{
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
				
				$session = new COSession();
				
				$_REQUEST['job'] = $job_it->getId();
				 
 				$command = '';
 				$classname = $job_it->get('ClassName');
 				$include = dirname(__FILE__).'/../commands/c_'.$classname.'.php';

				if ( !class_exists($classname) && file_exists($include) )
				{
 					include( $include );
				}
 				
				$start_date = SystemDateTime::date();
				
				if ( class_exists($classname) )
				{
				 	$command = new $classname;	
				}
				else
				{
					$parts = preg_split('/\//', $classname);
					
					if ( count($parts) > 1 )
					{
				 		$command = $plugins->getCommand( $parts[0], 'co', $parts[1] );
					}
				}
				 
				if ( is_object($command) )
				{
					$started_date = SystemDateTime::date();

					try
					{
						// pass concrete chunk to be processed
						if ( $_REQUEST['chunk'] != '' ) $command->setChunk(preg_split('/,/',$_REQUEST['chunk']));
						
						ob_start();
						$command->execute();

						if ( $_REQUEST['redirect'] == '' )
						{
							echo $job_it->getDisplayName().': '.translate('Выполнено').': '.SystemDateTime::date();
						}
						$result = ob_get_contents();

						if ( is_object($this->getLogger()) ) $this->getLogger()->info($result);
						
						getFactory()->getObject('co_JobRun')->add_parms( array ( 
							'ScheduledJob' => $job_it->getId(),
							'Result' => $result,
							'IsCompleted' => 'Y',
							'RecordCreated' => $started_date 
						));
					}
					catch( Exception $e )
					{
						if ( is_object($this->getLogger()) )
						{
							$this->getLogger()->error( get_class($command).': '.$e->getMessage() );
						}
					}
					
					// remove old results
					while ( true )
					{
						$cnt = $jobrun->getByRefArrayCount( 
							array( 'ScheduledJob' => $job_it->getId() ) );

						if ( $cnt < 21 )
						{
							break;
						}

						$run_it = $jobrun->getByRefArrayEarliest( 
							array( 'ScheduledJob' => $job_it->getId() ) );
						
						if ( $run_it->delete() < 1 ) break;
					}
				}
				
				$job_it->moveNext();
			}
		}
		
		$this->logFinish();
		
		$lock->Release();
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
	    DAL::Instance()->Query("check table co_JobRun");
	    DAL::Instance()->Query("repair table co_JobRun USE_FRM ");
	}
}
