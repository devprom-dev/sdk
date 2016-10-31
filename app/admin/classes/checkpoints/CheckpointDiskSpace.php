<?php

class CheckpointDiskSpace extends CheckpointEntryDynamic
{
	private $dir_path = '';
	
	function __construct( $dir_path )
	{
		$this->dir_path = $dir_path;
	}
	
	function execute()
	{
	    $this->setValue( round(disk_free_space($this->dir_path) / 1024 / 1024,0) );
	}
	
	function check()
	{
		return $this->getValue() == '' || $this->getValue() > 30;
	}
	
	function getRequired()
	{
		return true;
	}
	
	function getTitle()
	{
		return text(1135);
	}
	
	function getDescription()
	{
		return str_replace('%1', addslashes($this->dir_path), text(1136));
	}

    function getWarning()
    {
        return str_replace('%1', $this->dir_path, text(2258));
    }
}
