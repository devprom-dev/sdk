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
				
				$backup->full_delete(
					SERVER_BACKUP_PATH.basename($file_it->get('name'), '.zip').'/');
				
				if ( is_object($log) ) 
					$log->info( str_replace('%1', $file_it->get('name'), text(1230)) );
					
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

		$this->shrinkUndoLog();
		$this->logFinish();
	}
	
	function resetLicenseCache()
	{
	}

	function shrinkUndoLog()
	{
		$log = $this->getLogger();

		$interval = strtotime("-7 days");
		$undoPath = trim(UndoLog::Instance()->getDirectory(),'\/').'/*';
		foreach ( glob($undoPath) as $file ) {
			if (filemtime($file) <= $interval ) {
				if ( is_object($log) ) $log->info('Delete expired undo file: ' . $file);
				unlink($file);
			}
		}
	}
}