<?php

include_once SERVER_ROOT_PATH.'admin/classes/maintenance/BackupAndRecoveryOnWindows.php';

class StrategyUpdate
{
    var $file_name, $update;
    
    function __construct( $update_file_name )
    {
        $configuration = getConfiguration();
        
        $this->update = $configuration->getBackupAndRecoveryStrategy();
        
        $actual_path = SERVER_UPDATE_PATH.SystemDateTime::date('Y-m-d').".".$update_file_name;
        	
        if ( file_exists(SERVER_UPDATE_PATH.$update_file_name) )
        {
            unlink( $actual_path );
            
            rename( SERVER_UPDATE_PATH.$update_file_name, $actual_path );
        }
        
        $update_file_name = SystemDateTime::date('Y-m-d').".".$update_file_name;
        
        $this->update->log_file = fopen( SERVER_UPDATE_PATH.$update_file_name.'.log', "a+" );
        
        $this->file_name = $update_file_name; 
    }
    
    function getFileName()
    {
        return $this->file_name;
    }
    
    function getUpdate()
    {
        return $this->update;
    }
    
    function release()
    {
        fclose($this->update->log_file);
    }
}