<?php

class GlobalLock
{
	private $background_lock = null;
	private $maintenance_lock = null;
	
	function __construct()
	{
    	$this->background_lock = new LockFileSystem(BACKGROUND_TASKS_LOCK_NAME);
    	$this->maintenance_lock = new LockFileSystem(MAINTENANCE_LOCK_NAME);
	}
	
	function __destruct()
	{
		$this->release();
	}
	
	public function lock()
	{
        // waiting for background tasks to be completed
   	    $this->background_lock->Wait(120);
   	    
   	    // disable parallel background tasks to be running
   	    $this->background_lock->Lock();

   	    // lock UI before application update
        $this->maintenance_lock->Lock();
	}
	
	public function release()
	{
   	    $this->background_lock->Release();
        $this->maintenance_lock->Release();
	}
}