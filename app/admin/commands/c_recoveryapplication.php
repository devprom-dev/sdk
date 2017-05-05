<?php

include_once "MaintenanceCommand.php";
include_once SERVER_ROOT_PATH.'admin/install/InstallationFactory.php';
include_once SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class RecoveryApplication extends MaintenanceCommand
{
	function validate()
	{
		return true;
	}
	
	function create()
	{
	    $recovery = getConfiguration()->getBackupAndRecoveryStrategy();

	    $result = $recovery->recovery_database();

	    if( preg_match('/error\s+\d+/i', $result) ) $this->replyError(str_replace('%1', nl2br($result), text(1739)));
	    
	    $result = $recovery->recovery_htdocs();
	    
	    if ( $result != '' ) $this->replyError( str_replace('%1', $result, text(1051)) );
	    
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