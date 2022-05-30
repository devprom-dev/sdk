<?php

class ClearCache extends Installable
{
	private $cachePath = '';

	public function __construct() {
		parent::__construct();
		$this->setCachePath(CACHE_PATH);
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
        $lock->Lock();

        FileSystem::rmdirr($this->cachePath);
        FileSystem::rmdirr(SERVER_ROOT_PATH.'cache');

        $cmd = $this->getPhpExecutable() . ' "' . SERVER_ROOT_PATH . 'servicedesk/console" cache:warmup --env=prod 2>&1';
        $this->info('Warmup the symfony2 cache: ' . $cmd);
        exec($cmd, $output, $retCode);
        $this->info('Result: ' . $retCode . ', Output: ' . var_export($output, true));

        if ( function_exists('opcache_reset') ) opcache_reset();

        $lock->Release();
		return true;
	}

    public function getPhpExecutable()
    {
        if ($this->checkWindows()) {
            return '"'.SERVER_ROOT . '/php/php"';
        } else {
            return 'php';
        }
    }
}
