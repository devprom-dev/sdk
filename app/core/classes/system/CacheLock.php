<?php

class CacheLock extends LockFileSystem
{
	static $is_windows = null;

	function __construct( $timeout = 10 )
	{
		parent::__construct('cache-global-lock');
		if ( is_null(self::$is_windows) ) {
			self::$is_windows = EnvironmentSettings::getWindows();
		}
		$this->Wait($timeout);
		$this->Lock();
	}
	
	function __destruct() {
		$this->Release();
	}

    public function Wait( $timeout, $callable = null )
    {
        while( $this->Locked($timeout) ) {
			if ( self::$is_windows ) {
				sleep(1);
			} else {
				usleep(10000);
			}
		}
    }
}