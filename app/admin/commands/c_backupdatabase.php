<?php
include_once "MaintenanceCommand.php";
include SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class BackupDatabase extends MaintenanceCommand
{
	function validate() {
		return true;
	}

	function create()
	{
	    try {
            getConfiguration()->getBackupAndRecoveryStrategy()->backup_database();
        }
        catch( \Exception $e ) {
	        $this->replyError($e->getMessage());
        }

		$this->replyRedirect( '?action=backupapplication&parms='.SanitizeUrl::parseUrl($_REQUEST['parms']) );
	}
}
