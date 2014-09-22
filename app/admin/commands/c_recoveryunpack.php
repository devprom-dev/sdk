<?php

include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class RecoveryUnpack extends MaintenanceCommand
{
    var $backup_file_name;
    
	function validate()
	{
		return true;
	}

	function getStrategy()
	{
	    $this->backup_file_name = $_REQUEST['parms'];
	    
	    $configuration = getConfiguration();
	     
	    $recovery = $configuration->getBackupAndRecoveryStrategy();
	     
	    $recovery->log_file = fopen( SERVER_BACKUP_PATH.$this->backup_file_name.'.log', "a+" );
	     
	    return $recovery;
	}
	
	function create()
	{
	    global $_REQUEST;

	    $recovery = $this->getStrategy();

		$recovery->recovery_unzip( $this->backup_file_name );
	    
	    fclose($recovery->log_file);
	    
		$this->replyRedirect( '?action=recoveryfiles&parms='.SanitizeUrl::parseUrl($_REQUEST['parms']) );
	}
}
