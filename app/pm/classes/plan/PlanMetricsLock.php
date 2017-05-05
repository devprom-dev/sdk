<?php

class PlanMetricsLock extends LockFileSystem
{
	function __construct( $timeout = 30 )
	{
		parent::__construct('planmetrics-lock');
		$this->Wait($timeout);
		$this->Lock();
	}
	
	function __destruct() {
		$this->Release();
	}

    public function Wait( $timeout, $callable = null )
    {
        if ( EnvironmentSettings::getWindows() ) return;
        while( $this->Locked($timeout) ) {
            time_nanosleep(0, 500000000);
		}
    }
}