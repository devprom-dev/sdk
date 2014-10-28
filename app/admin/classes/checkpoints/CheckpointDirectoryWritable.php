<?php

class CheckpointDirectoryWritable extends CheckpointEntryDynamic
{
    function getRequired()
    {
        return true;
    }
	
	function execute()
    {
    	$check_result = "1";
    	$self = $this;
    	
    	array_walk( $this->getFolders(), 
    			function( $value ) use (&$check_result, $self) {
    					if ( !$self->isWritable( $value ) ) $check_result = "0";
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
    	$self = $this;
    	
    	array_walk( $this->getFolders(), 
    			function( $value ) use (&$text, $self) {
    					$line = addslashes($value);
    					if ( !$self->isWritable( $value ) ) $line = "<b>".$line."</b>";
    					$text .= $line."<br/>";
    			}
		);
    	
    	return $text;
    }
    
    function isWritable( $path )
    {
    	$name = tempnam($path, "tmp");
    	
    	file_put_contents($name, "tmp");
    	
    	$result = file_get_contents($name) == "tmp";
    	
    	if ( $result ) unlink($name);
    	
    	return $result;
    }
    
    function getFolders()
    {
    	return array (
    			SERVER_BACKUP_PATH,
    			SERVER_UPDATE_PATH,
    			SERVER_FILES_PATH,
    			SERVER_ROOT_PATH,
    			SERVER_ROOT_PATH.'plugins'
    	);
    }
}
