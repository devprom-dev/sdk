<?php

class CheckpointEntryBase
{
    var $enabled = true;

    function getUid()
    {
        return md5(strtolower(get_class($this)));
    }

    function getTitle()
    {
        return '';
    }

    function getDescription()
    {
        return '';
    }

    function getUrl()
    {
        return '/admin/checks.php';
    }

    function getValue()
    {
        return '';
    }

    function getRequired()
    {
        return false;
    }
    
    function enabled()
    {
        return $this->enabled;
    }

    function disable()
    {
        return $this->enabled = false;
    }

    function enable()
    {
        return $this->enabled = true;
    }

    function check()
    {
        return false;
    }
    
    function notificationRequired()
    {
    	return true;
    }

	function getLogger()
	{
 		try 
 		{
 			if ( !is_object($this->logger) )
 			{
 				$this->logger = Logger::getLogger('Commands');
 			}
 			
 			return $this->logger;
 		}
 		catch( Exception $e)
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
	}
	
	function error( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->error( get_class($this).': '.$message );
	}
	
	function debug( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->debug( get_class($this).': '.$message );
	}
	
	function info( $message )
	{
		$log = $this->getLogger();
		
		if ( !is_object($log) ) return;
		
		$log->info( get_class($this).': '.$message );
	}    

    function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false
            || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
}