<?php

include "SCMCredentials.php";
include "SCMFileRegistry.php";
include "SCMCommitRegistry.php";
include "SCMFileActionRegistry.php";

abstract class SCMConnector
{
 	var $credentials, $object;
 	
 	abstract function getDisplayName();
 	
 	abstract static function checkPrerequisites();
 	
 	function init( SCMCredentials $credentials )
 	{
 		$this->credentials = $credentials; 
 	}
 	
 	function resetCredentials()
 	{
 	}
 	
 	function getCredentials()
 	{
 		return $this->credentials;
 	}
 	
 	function getCredentialsParmDescription( $parm )
 	{
 		return '';
 	}
 	
 	function transformUrl( $url, $path )
 	{
		return array( $url, $path );
 	}
 	
 	function buildPath( $path, $filename )
 	{
 		return $path.'/'.$filename;
 	}
 	
 	function log( $message )
 	{
 		if ( $message == "" ) return;
 		
 		try
 		{
	 		Logger::getLogger('SCM')->info( get_class($this).": ".$message );
 		}
 		catch( Exception $e )
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
 	}
 	
 	function debug( $message )
 	{
 		if ( $message == "" ) return;
 		
 		try
 		{
	 		Logger::getLogger('SCM')->debug( get_class($this).": ".$message );
 		}
 		catch( Exception $e )
 		{
 			error_log('Unable initialize logger: '.$e->getMessage());
 		}
 	}
 	
 	function getFiles( $path )
	{
 		$registry = new SCMFileRegistry();
 		
 		return $registry->getAll();
	}
	
	function getRecentLogs( $version = '', $limit = 10 )
	{
 		$registry = new SCMCommitRegistry();
 		
 		return $registry->getAll();
	}
	
	function getRepositoryLog( $from = 0, $to = -1 )
	{
 		$registry = new SCMCommitRegistry();
 		
 		return $registry->getAll();
	}
	
	function getFileLogs( $path, $initial_version = 0, $final_version = -1)
	{
 		$registry = new SCMCommitRegistry();
 		
 		return $registry->getAll();
	}
	
	function getVersionFiles( $version )
	{
 		$registry = new SCMFileActionRegistry();
 		
 		return $registry->getAll();
	}
	
	function getTextFile( $path, $version = '' )
	{
	}

	function getBinaryFile( $path, $version = '' )
	{
	}
	
	function setObject( $object )
	{
		$this->object = $object;
	}
	
	function getObject()
	{
		return $this->object;
	}
	
     /**
      * @param SubversionIterator $iterator
      * @return array
      */
     function mapIteratorDataToDbAttributes($iterator)
     {
         return array(
             'Version' => $iterator->get('Version'),
             'Description' => $iterator->get('Comment'),
             'CommitDate' => $iterator->get('RecordModified'),
             'Author' => $iterator->get('Author'),
             );
     }

     /**
      * @return bool
      */
     function hasNumericVersion() {
         return false;
     }
     
    protected static function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false
            || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }
}