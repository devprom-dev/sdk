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
		
		if ( $_REQUEST['parms'] == '' )
		{
		    move_uploaded_file( $_FILES['Update']['tmp_name'], $filepath );
		}

		$configuration = getConfiguration();
		
		$strategy = $configuration->getBackupAndRecoveryStrategy();
		
		$strategy->update_clean();
		
		if ( is_dir(SERVER_UPDATE_PATH.'htdocs') )
		{
			$this->replyError(str_replace('%1', SERVER_UPDATE_PATH.'htdocs', text(1433)));
		}
		
		if ( is_dir(SERVER_UPDATE_PATH.'devprom') )
		{
			$this->replyError(str_replace('%1', SERVER_UPDATE_PATH.'htdocs', text(1433)));
		}
		
		$result = $strategy->update_unzip( $pathinfo['basename'] );
			
		if ( $result != '' ) $this->replyError($result);

		DAL::Instance()->Reconnect();
		
		$this->checkUpdateIsValid();

        $this->checkPHPVersion();

		$this->checkPlugins();
		
		$this->checkPoints();
		
		$strategy = new StrategyUpdate($pathinfo['basename']);
		
		$this->checkRequiredVersion( $strategy->getUpdate() );
		
		$url_to_continue = 'backups.php?action=backupdatabase&parms=update,'.$pathinfo['basename'];

		$this->replyRedirect( $url_to_continue, text(1256) );
	}
	
	function checkUpdateIsValid()
	{
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
		global $model_factory;
		
		$update_version = '';
	    
	    $update->update_getinfo( $update_version );
	    
	    if ( $update_version != '' )
	    {
	        $update_it = $model_factory->getObject('cms_Update')->getLatest();
	        	
	        $current_version = $update_it->getDisplayName();
	    
	        $parts1 = preg_split('/\./', $update_version);
	        $parts2 = preg_split('/\./', $current_version);
	        	
	        $pad = max(count($parts1), count($parts2));
	        	
	        $version_parts = array_reverse(array_pad($parts1, $pad, 0));
	        $installed_parts = array_reverse(array_pad($parts2, $pad, 0));
	    
	        $update_number = 0;
	        foreach( $version_parts as $key => $part ) $update_number += pow(100, $key) * $part;
	    
	        $installed_number = 0;
	        foreach( $installed_parts as $key => $part ) $installed_number += pow(100, $key) * $part;
	    
	        $previous_version = $update_number <= $installed_number;
	    
	        if ( $previous_version )
	        {
	            $this->replyError(str_replace('%1', $update_version, str_replace('%2', $current_version, text(404))));
	        }
	    }
	    	
	    $update_version = '';
	    
	    $update->update_getrequired( $update_version );
	    
	    if ( $update_version != "" )
	    {
	        $versions_array = preg_split('/,/', trim($update_version, " \r\n"));
	    
	        $update_it = $model_factory->getObject('cms_Update')->getByRefArray( array(
	        		'Caption' => $versions_array 
	        ));
	    
	        if ( $update_it->count() < 1 )
	        { 
	            $this->replyError(str_replace('%1', join($versions_array, "; "), text(1050)));
	        }
	    }
	}

	protected function checkPHPVersion()
    {
        $file_path = SERVER_UPDATE_PATH.'devprom/php.txt';
        if ( !file_exists($file_path) ) return;
        $requiredVersion = file_get_contents($file_path);
        if ( TextUtils::versionToString(phpversion()) < TextUtils::versionToString($requiredVersion) ) {
            $this->replyError(str_replace('%1', $requiredVersion, str_replace('%2', phpversion(), text(2226))));
        }
    }
}
