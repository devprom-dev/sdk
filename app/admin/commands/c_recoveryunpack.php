<?php
include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class RecoveryUnpack extends MaintenanceCommand
{
    var $backup_file_name;
    
	function validate()	{
		return true;
	}

	function create()
	{
	    try {
	        $strategy = getConfiguration()->getBackupAndRecoveryStrategy();
            $strategy->recovery_clean($_REQUEST['parms']);
            $strategy->recovery_unzip($_REQUEST['parms']);
            $this->replyRedirect( '?action=recoveryfiles&parms='.SanitizeUrl::parseUrl($_REQUEST['parms']) );
        }
        catch( \Exception $e ) {
	        $this->replyError($e->getMessage());
        }
	}
}
