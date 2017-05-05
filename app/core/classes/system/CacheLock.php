<?php

class CacheLock extends LockFileSystem
{
	function __construct( $timeout = 3 )
	{
		parent::__construct('cache-global-lock');
		$this->Wait($timeout);
		$this->Lock();
	}
	
	function __destruct() {
		$this->Release();
	}

    public function Wait( $timeout, $callable = null )
    {
        while( $this->Locked($timeout) ) {
            time_nanosleep(0, 500000000);
        }
    }
}