<?php

class CopyConfigurationFiles extends Installable
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
	        'plugins/ee/settings.php',
	        'plugins/ee/settings_ldap_ad.php',
	        'plugins/ee/settings_ldap_apacheds.php',
	        'plugins/ee/settings_ldap_openldap.php',
	        'plugins/wrtfckeditor/custom.css'
	    );
	    
	    if ( $this->checkWindows() )
	    {
	        $map['logger-win.xml'] = 'logger.xml';
	        $map['dlls/php_fileinfo.dll'] = '../../../php/extensions/php_fileinfo.dll';
	        $map['dlls/php_pdo_mysql.dll'] = '../../../php/extensions/php_pdo_mysql.dll';
	        $map['dlls/php_opcache.dll'] = '../../../php/extensions/php_opcache.dll';
	    } 
	    else
	    {
	        $map['logger-linux.xml'] = 'logger.xml';
	    }
	       
	    foreach( $map as $source => $target )
	    {
	        $this->copyFile( is_numeric($source) ? $target : $source, $target );
	    }
	    
	    if ( $this->checkWindows() )
	    {
	    	$this->setupWindowsLoggers();
	    }
	    else
	    {
	    	$this->setupLinuxLoggers();
	    }
	    
		return true;
	}
	
	function copyFile( $source_filename, $target_path )
	{
	    $local_file = DOCUMENT_ROOT.'conf/'.$target_path;
	    
	    if ( file_exists($local_file) )
	    {
	        $this->info( 'Skip file '.$local_file );
	        
	        return true;
	    }
	    
	    if ( !is_dir(dirname($local_file)) )
	    {
	        if ( !@mkdir( dirname($local_file), 0775, true ) )
	        {
	            $this->error( var_export(error_get_last(), true) );
	        }
	    }
	    
	    $source_file = SERVER_ROOT_PATH.'templates/config/'.$source_filename;
	    
	    $this->info( 'Copy file '.$source_file.' to '.$local_file );
	    
	    if ( !@copy($source_file, $local_file) )
	    {
	        $this->error( var_export(error_get_last(), true) );
	    }
	    
	    return true;
	}
	
	function setupWindowsLoggers()
	{
		$local_file = DOCUMENT_ROOT.'conf/logger.xml';
		
		file_put_contents( $local_file,
				preg_replace_callback(
						'/name="file"\s+value="([^"]+)"/im',
						function( $matches ) {
								return 'name="file" value="'.SERVER_ROOT.'/apache/logs/'.basename($matches[1]).'"';
						},
						file_get_contents($local_file)
				)		
	    );
	}
	
	function setupLinuxLoggers()
	{
		$default_path = '/var/log/devprom';
		
		mkdir($default_path, 0755, true);
		
		if ( is_dir($default_path) ) return;

		$local_dir = SERVER_ROOT.'/logs';

		mkdir($local_dir, 0755, true);

		if ( !is_dir($local_dir) )
		{
			// shared hosting
			$local_dir = DOCUMENT_ROOT.'logs';

			mkdir($local_dir, 0755, true);
		}
		
		$settings_file = DOCUMENT_ROOT.'conf/logger.xml';
		
		file_put_contents($settings_file, str_replace($default_path, $local_dir, file_get_contents($settings_file))); 
	}
}
