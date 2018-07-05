<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE
include_once "CacheEngine.php";

class CacheEngineFS extends CacheEngine
{
	private $cache_in_memory;
	
	function __construct() {
		parent::__construct();
		
		$this->checkDirectories();
		$this->cache_in_memory = array();
	}

	function __wakeup() {
        $this->checkDirectories();
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
        if ( $category == '' ) throw new Exception('Cache path is required');
		
	  	if ( !isset($this->cache_in_memory[$category][$key]) ) {
		  	$this->cache_in_memory[$category][$key] = @file_get_contents($this->getFilePath($key, $category));
	  	}
		return unserialize($this->cache_in_memory[$category][$key]);
	}
	
	function set( $key, $value, $category = '' )
	{
        if ( $category == '' ) throw new Exception('Cache path is required');

		if ( $this->getReadonly() ) return;
        if ( !is_dir($this->cache_dir) ) return;

		$dir_path = $this->getFilePath('', $category);
		if ( !file_exists($dir_path) ) @mkdir($dir_path, 0777, true);
		
		@file_put_contents(
		    $this->getFilePath($key, $category),
            $this->cache_in_memory[$category][$key] = serialize($value)
        );
	}
	
	function reset( $key, $category = '' )
	{
        if ( $category == '' ) throw new Exception('Cache path is required');

        if ( $this->getReadonly() ) return;
        if ( !is_dir($this->cache_dir) ) return;

		unset($this->cache_in_memory[$category][$key]);
		@unlink($this->getFilePath($key, $category));
	}
	
	function invalidate( $path = '' )
	{
        $lock = new CacheLock();
        $lock->Lock();

        if ( $path == '' ) {
            unset($this->cache_in_memory);
            \FileSystem::rmdirr( $this->cache_dir );
        }
        else {
            unset($this->cache_in_memory[$path]);
            \FileSystem::rmdirr( $this->getFilePath('', $path) );
        }
        if ( function_exists('opcache_reset') ) opcache_reset();
	}
	
	protected function getFilePath( $key, $category )
	{
		$key = $category != '' ? $category.'/'.$key : $key;
		return $this->cache_dir.$key;
	}
}
