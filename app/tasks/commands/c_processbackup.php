<?php

include_once SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';
include_once SERVER_ROOT_PATH.'cms/c_iterator_file.php';

class ProcessBackup extends TaskCommand
{
 	function execute()
	{
		global $model_factory;

		$configuration = getConfiguration();
		$backup = $configuration->getBackupAndRecoveryStrategy();
		
		$this->logStart();
		
		// trial license should be revalidated every day
		$this->resetLicenseCache();

        // remove old undo log files
        $this->shrinkUndoLog();

        // remove old test results
        $this->shrinkTests();

        // remove old notifications of objects changes
        $this->shrinkChangeNotifications();

		$job = $model_factory->getObject('co_ScheduledJob');
		$job_it = $job->getExact($_REQUEST['job']);
		
		$parameters = $job_it->getParameters();

		if ( $parameters['limit'] != '' && $parameters['limit'] > 0 )
		{
			$log = $this->getLogger();
			
			if ( is_object($log) ) 
				$log->info( str_replace('%1', $parameters['limit'], text(1229)) );
			
			$file_it = new IteratorFile($job, SERVER_BACKUP_PATH);
			$file_it->sortCreated();
			
			$remove = $file_it->count() - $parameters['limit'];
			while( $remove >= 0 )
			{
				if ( $file_it->get('name') == '' ) break;
				
				unlink(SERVER_BACKUP_PATH.$file_it->get('name'));
                FileSystem::rmdirr(SERVER_BACKUP_PATH.basename($file_it->get('name'), '.zip'));

				if ( is_object($log) ) {
                    $log->info( str_replace('%1', $file_it->get('name'), text(1230)) );
                }

				$file_it->moveNext();
				$remove--;
			}
		}

		$backup->backup_database();
		$backup->backup_htdocs();

		$backup->zip();
		$backup->backup_files();

		$backup_cls = $model_factory->getObject('cms_Backup');
		$backup_cls->add_parms( array (
			'Caption' => text(1173),
			'BackupFileName' => $backup->getBackupFileName()
		));

		$this->logFinish();
	}
	
	function resetLicenseCache()
	{
	}

	function shrinkUndoLog()
	{
		$log = $this->getLogger();

		$interval = strtotime("-7 days");
		$undoPath = rtrim(UndoLog::Instance()->getDirectory(),'\/').'/*';
        if ( is_object($log) ) $log->info('Shrink undo log at ' . $undoPath . ' for '.$interval);

		foreach ( glob($undoPath) as $file ) {
			if (filemtime($file) <= $interval ) {
				if ( is_object($log) ) $log->info('Delete expired undo file: ' . $file);
				unlink($file);
			}
		}

		\FileSystem::rmdirr( SERVER_FILES_PATH . 'tmp' );
        \FileSystem::rmdirr( SERVER_FILES_PATH . 'upload' );
	}

	function shrinkTests()
    {
        $lastDate = date("Y-m-d", strtotime(defined('OLDEST_TEST_RUNS') ? OLDEST_TEST_RUNS : "-1 month"));

        DAL::Instance()->Query("
          DELETE FROM pm_TestCaseExecution WHERE RecordCreated < '".$lastDate."'
             AND NOT EXISTS (SELECT 1 FROM pm_ChangeRequestTrace t 
                              WHERE t.ObjectId = pm_TestCaseExecutionId
                                AND t.ObjectClass = 'TestCaseExecution')
        ");
        DAL::Instance()->Query("
          DELETE FROM pm_Test WHERE RecordCreated < '".$lastDate."'
             AND NOT EXISTS (SELECT 1 FROM pm_TestCaseExecution e WHERE e.Test = pm_TestId)
        ");
    }

    function shrinkChangeNotifications()
    {
        $lastDate = date("Y-m-d", strtotime("-1 month"));
        DAL::Instance()->Query(" DELETE FROM ObjectChangeNotification WHERE RecordCreated < '".$lastDate."' ");
    }
}