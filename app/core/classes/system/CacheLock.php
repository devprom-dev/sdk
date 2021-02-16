<?php

class CacheLock extends LockFileSystem
{
	function __construct( $timeout = 5 )
	{
		parent::__construct('cache-global-lock');
		$this->Wait($timeout);
	}
	
    public function Wait( $timeout, $callable = null )
    {
        while( $this->Locked($timeout) ) {
            time_nanosleep(0, 500000000);
        }
    }
}