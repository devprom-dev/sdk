<?php

class CheckpointDirectoryWritable extends CheckpointEntryDynamic
{
    function execute()
    {
    	$check_result = "1";
    	
    	array_walk( $this->getFolders(), 
    			function( $value ) use (&$check_result) {
    					if ( !is_writable( $value ) ) $check_result = "0";
    			}
		);
    	
        $this->setValue( $check_result );
    }

    function getTitle()
    {
        return text(1131);
    }

    function getDescription()
    {
    	$text = '';
    	
    	array_walk( $this->getFolders(), 
    			function( $value ) use (&$text) {
    					$line = addslashes($value);
    					if ( !is_writable( $value ) ) $line = "<b>".$line."</b>";
    					$text .= $line."<br/>";
    			}
		);
    	
    	return $text;
    }
    
    function getFolders()
    {
    	return array (
    			SERVER_BACKUP_PATH,
    			SERVER_UPDATE_PATH,
    			SERVER_FILES_PATH,
    			SERVER_ROOT_PATH.'common.php'
    	);
    }
}
