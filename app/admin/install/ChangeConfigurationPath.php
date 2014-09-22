<?php

class ChangeConfigurationPath extends Installable
{
	// checks all required prerequisites
	function check()
	{
		return true;
	}

	// skip install actions
	function skip()
	{
		return false;
	}

	// cleans after the installation script has been executed
	function cleanup()
	{
	}

	// makes install actions
	function install()
	{
	    $map = array (
	        'plugins/ee/system/settings.php' => 'plugins/ee/settings.php',
	        'plugins/ee/system/settings_ldap_ad.php' => 'plugins/ee/settings_ldap_ad.php',
	        'plugins/ee/system/settings_ldap_apacheds.php' => 'plugins/ee/settings_ldap_apacheds.php',
	        'plugins/ee/system/settings_ldap_openldap.php' => 'plugins/ee/settings_ldap_openldap.php',
	        'plugins/wrtfckeditor/ckeditor/custom.css' => 'plugins/wrtfckeditor/custom.css',
	        'admin/system/checkpointsystem.ini' => 'checkpointsystem.ini'
	    );
	    
	    foreach( $map as $source => $target )
	    {
	        $this->moveFile( $source, $target );
	    }
	    
		return true;
	}
	
	function moveFile( $source_filename, $target_path )
	{
	    $local_file = DOCUMENT_ROOT.'conf/'.$target_path;
	    
	    if ( file_exists($local_file) )
	    {
	        $this->info( 'Skip file '.$local_file );
	        
	        return true;
	    }

	    if ( !is_dir(dirname($local_file)) )
	    {
	        if( !@mkdir( dirname($local_file), 0775, true ) )
	        {
	            $this->error( var_export(error_get_last(), true) );
	        }
	    }

	    if ( !file_exists(SERVER_ROOT_PATH.$source_filename) ) return true;
	    
	    $source_file = SERVER_ROOT_PATH.$source_filename;
	    
	    $this->info( 'Move file '.$source_file.' to '.$local_file );
	    
	    if ( !@rename($source_file, $local_file) )
	    {
	        $this->error( var_export(error_get_last(), true) );
	    }
	    
	    return true;
	}
}
