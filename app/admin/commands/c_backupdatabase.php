<?php

include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class BackupDatabase extends MaintenanceCommand
{
	function validate()
	{
		return true;
	}

	function create()
	{
	    global $_REQUEST;

	    $configuration = getConfiguration();
	    
	    $backup = $configuration->getBackupAndRecoveryStrategy();

	    $backup->backup_database();
	     
		$this->replyRedirect( '?action=backupapplication&parms='.SanitizeUrl::parseUrl($_REQUEST['parms']) );
	}
}
