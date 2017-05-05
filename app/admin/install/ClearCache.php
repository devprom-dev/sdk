<?php

class ClearCache extends Installable
{
	private $cachePath = '';

	public function __construct() {
		parent::__construct();
		$this->setCachePath(defined('CACHE_PATH') ? CACHE_PATH : SERVER_ROOT_PATH.'cache/');
	}

	public function setCachePath( $path ) {
		$this->cachePath = $path;
	}

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
		$lock = new CacheLock();
        FileSystem::rmdirr($this->cachePath);
        $lock->Release();

		return true;
	}
}
