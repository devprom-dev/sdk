<?php

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class CacheLock extends LockFileSystem
{
	function __construct()
	{
		parent::__construct('cache-global-lock');
	}
	
	function __destruct()
	{
		$this->Release();
	}
}