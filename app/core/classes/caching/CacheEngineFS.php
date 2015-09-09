<?php

include_once "CacheEngine.php";

class CacheEngineFS extends CacheEngine
{
	var $cache_dir, $cache_in_memory;
	
	function __construct( $path = 'global' )
	{
		parent::__construct($path);
		
		$this->checkDirectories();
		
		$this->cache_in_memory = array();
	}
	
	function checkDirectories()
	{
		$this->cache_dir = CACHE_PATH.'/appcache/';
		
		if ( !is_dir($this->cache_dir) )
		{
	        $was_mask = umask(0);
		    @mkdir($this->cache_dir, 0777, true);
		    chmod($this->cache_dir, 0777);
		    umask($was_mask);
		}
	}
	
	function get( $key, $category = '' )
	{
		if ( $category == '' ) $category = $this->getDefaultPath();
		
	  	if ( !isset($this->cache_in_memory[$category][$key]) )
	  	{
		  	$this->cache_in_memory[$category][$key] = @file_get_contents($this->getFilePath($key, $category));
	  	}
	  	 
		return unserialize($this->cache_in_memory[$category][$key]);
	}
	
	function set( $key, $value, $category = '' )
	{
		if ( $this->getReadonly() )
		{
			return;
		}

		if ( $category == '' ) $category = $this->getDefaultPath();
		
		$dir_path = $this->getFilePath('', $category);
		
		if ( !file_exists($dir_path) ) @mkdir($dir_path, 0777, true);
		
		$this->cache_in_memory[$category][$key] = serialize($value);
		
		@file_put_contents($this->getFilePath($key, $category), $this->cache_in_memory[$category][$key]);
	}
	
	function reset( $key, $category = '' )
	{
		if ( $category == '' ) $category = $this->getDefaultPath();
		
		unset($this->cache_in_memory[$category][$key]);
	    
		$file_path = $this->getFilePath($key, $category);
		
		@unlink($file_path);
	}
	
	function truncate( $category = '' )
	{
		if ( $category == '' ) $category = $this->getDefaultPath();
		
		$this->rrmdir( $this->getFilePath('', $category) ); 
	}
	
	function drop()
	{
		$this->rrmdir( $this->cache_dir );
		
		$this->checkDirectories();
	}
	
	protected function getFilePath( $key, $category )
	{
		$key = $category != '' ? $category.'/'.$key : $key;
		
		return $this->cache_dir.$key;
	}
	
	protected function rrmdir($dir)
	{
	    foreach(glob($dir . '*') as $file)
	    {
	        if(is_dir($file))
	            $this->rrmdir($file.'/');
	        else
	            @unlink($file);
	    }
	   	@rmdir($dir);
	}
}
