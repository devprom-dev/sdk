<?php
include_once "MaintenanceCommand.php";
include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class RecoveryApplication extends MaintenanceCommand
{
	function validate() {
		return true;
	}
	
	function create()
	{
        $backupObject = getFactory()->getObject('cms_Backup');
        $backupIt = $backupObject->getRegistry()->Query(
            array(
                new FilterAttributePredicate('BackupFileName', $_REQUEST['parms'])
            )
        );

	    $recovery = getConfiguration()->getBackupAndRecoveryStrategy();
	    try {
            $recovery->recovery_database();
            $recovery->recovery_htdocs();
        }
        catch( \Exception $e ) {
	        $this->replyError($e->getMessage());
        }

	    // clear old cache
	    InstallationFactory::getFactory();
	    $clear_cache_action = new ClearCache();
	    $clear_cache_action->install();
	    
	    // reset opcache after application files have been restored
	    if ( function_exists('opcache_reset') ) opcache_reset();

	    $recovery->recovery_clean($_REQUEST['parms']);
	    
		$this->replyRedirect( '?' );
	}
}