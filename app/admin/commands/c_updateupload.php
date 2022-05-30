<?php
include_once "MaintenanceCommand.php";
include_once SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';
include_once SERVER_ROOT_PATH.'admin/classes/StrategyUpdate.php';
include_once SERVER_ROOT_PATH.'admin/classes/CheckpointFactory.php';

////////////////////////////////////////////////////////////////////////////
class UpdateUpload extends MaintenanceCommand
{
	function validate()
	{
		global $_FILES;

		if ( $_REQUEST['parms'] != '' && file_exists(SERVER_UPDATE_PATH.$_REQUEST['parms']) ) return true;
		
		if ( !is_uploaded_file($_FILES['Update']['tmp_name']) )
		{
			$this->replyError( str_replace('%1',
				\FileSystem::translateError($_FILES['Update']['error']), text(1255)) );
		}
		
		return true;
	}

	function create()
	{
		$pathinfo = pathinfo($_FILES['Update']['name'] == '' ? $_REQUEST['parms'] : $_FILES['Update']['name']);
		
		$filepath = SERVER_UPDATE_PATH.$pathinfo['basename'];
		
		if ( $_REQUEST['parms'] == '' ) {
		    move_uploaded_file( $_FILES['Update']['tmp_name'], $filepath );
		}

		if ( filesize($filepath) < 100 ) {
            $this->replyError(text(2933));
        }

		$configuration = getConfiguration();
		
		$strategy = $configuration->getBackupAndRecoveryStrategy();
		
		$strategy->update_clean();
		
		if ( is_dir(SERVER_UPDATE_PATH.'htdocs') ) {
			$this->replyError(str_replace('%1', SERVER_UPDATE_PATH.'htdocs', text(1433)));
		}
		
		if ( is_dir(SERVER_UPDATE_PATH.'devprom') ) {
			$this->replyError(str_replace('%1', SERVER_UPDATE_PATH.'htdocs', text(1433)));
		}

		try {
            $strategy->update_unzip( $pathinfo['basename'] );
        }
		catch( \Exception $e ) {
		    $this->replyError($e->getMessage());
        }

		unlink($filepath);

		DAL::Instance()->Reconnect();
		
		$this->checkUpdateIsValid();

        $this->checkPHPVersion();

		$this->checkPlugins();
		
		$this->checkPoints();
		
		$strategy = new StrategyUpdate($pathinfo['basename']);
		
		$this->checkRequiredVersion( $strategy->getUpdate() );

		if ( defined('SKIP_BACKUP_BEFORE_UPDATE') && SKIP_BACKUP_BEFORE_UPDATE ) {
            $url_to_continue = '/admin/updates.php?action=updateapplication&parms='.$pathinfo['basename'];
        }
		else {
            $url_to_continue = '/admin/backups.php?action=backupdatabase&parms=update,'.$pathinfo['basename'];
        }

		$this->replyRedirect( $url_to_continue, text(1256) );
	}
	
	function checkUpdateIsValid()
	{
        $log = $this->getLogger();
        if ( is_object($log) ) $log->info("checkUpdateIsValid");

	    $required_dirs = count(scandir(SERVER_UPDATE_PATH.'htdocs')) > 2
	        && count(scandir(SERVER_UPDATE_PATH.'devprom')) > 2;
	    	
	    if ( !$required_dirs )
	    {
	        $this->replyError(text(1301));
	    }
	}
	
	function checkPlugins()
	{
	    global $plugins;

        $log = $this->getLogger();
        if ( is_object($log) ) $log->info("checkPlugins");

	    if ( !file_exists(SERVER_UPDATE_PATH.'htdocs/common.php') ) return;
	    
        // check plugins to be updated with core are in the core update
        $items = $plugins->getNamespaces();
    
        foreach( $items as $plugin )
        {
            if ( $plugin->IsUpdatedWithCore() )
            {
                $file_name = SERVER_UPDATE_PATH.'htdocs/plugins/'.$plugin->getFileName();
    
                if ( !file_exists( $file_name ) )
                {
                    $this->replyError( str_replace('%1', str_replace(',', ';', $plugin->getCaption()), text(1231)) );
                }
            }
        }
	}
	
	function checkPoints()
	{
        $log = $this->getLogger();
        if ( is_object($log) ) $log->info("checkPoints");

	    // check all required checkpoints are passed
			$checkpointFactory = new CheckpointFactory(SERVER_UPDATE_PATH.'htdocs/');

			$system = $checkpointFactory->getCheckpoint( 'CheckpointSystem' );

			if ( !is_object($system) ) return;

			$system->executeDynamicOnly();

            $failed = false;
			if ( !$system->checkRequired( $failed ) )
			{
	        $description = array();
	         
	        foreach( $failed as $entry )
	        {
	            $description[] = ' - '.$entry->getDescription().' ['.$entry->getTitle().']';
	        }
	         
	        $this->replyError( str_replace('%1', '<br/>'.str_replace(',', ';', join('<br/>', $description)), text(1300)) );
	    }
	}
	
	function checkRequiredVersion( $update )
	{
        $log = $this->getLogger();
        if ( is_object($log) ) $log->info("checkRequiredVersion");

        $update_it = getFactory()->getObject('cms_Update')->getLatest();
        $current_version = $update_it->getDisplayName();

		$update_version = '';
	    $update->update_getinfo( $update_version );
	    
	    if ( $update_version != '' )
	    {
	        if ( version_compare($update_version, $current_version) < 0 ) {
                $this->replyError(str_replace('%1', $update_version, str_replace('%2', $current_version, text(404))));
            }
	    }
	    	
	    $required_version = '';
	    $update->update_getrequired( $required_version );
	    
	    if ( $required_version != "" )
	    {
	        $versions_array = preg_split('/,/', trim($required_version, " \r\n"));
            $required_version = array_shift($versions_array);

            if ( version_compare($required_version, $current_version) > 0 ) {
                $this->replyError(str_replace('%1', join($versions_array, "; "), text(1050)));
            }
	    }
	}

	protected function checkPHPVersion()
    {
        $log = $this->getLogger();
        if ( is_object($log) ) $log->info("checkPHPVersion");

        $file_path = SERVER_UPDATE_PATH.'devprom/php.txt';
        if ( !file_exists($file_path) ) return;
        $requiredVersion = file_get_contents($file_path);
        if ( TextUtils::versionToString(phpversion()) < TextUtils::versionToString($requiredVersion) ) {
            $this->replyError(str_replace('%1', $requiredVersion, str_replace('%2', phpversion(), text(2226))));
        }
    }
}
