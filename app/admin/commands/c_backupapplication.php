<?php

include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class BackupApplication extends MaintenanceCommand
{
	function validate()
	{
		return true;
	}

	function create()
	{
	    $configuration = getConfiguration();
	    
	    $backup = $configuration->getBackupAndRecoveryStrategy();

	    $result = $backup->backup_htdocs();
	    
		$result == '' 
		    ? $this->replyRedirect( '?action=backupcomplete&parms='.SanitizeUrl::parseUrl($_REQUEST['parms']) )
		    : $this->replyError( $result );
	}
}
