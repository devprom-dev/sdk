<?php

include_once SERVER_ROOT_PATH.'core/classes/system/GlobalLock.php';

class MaintenanceCommand extends CommandForm
{
	private $global_lock = null;
	
	public function __construct()
	{
    	$this->global_lock = new GlobalLock();
    	
    	parent::__construct();
	}

	public function execute()
	{
		$this->global_lock->lock();
		
   	    parent::execute();
   	    
		$this->global_lock->release();
   	}

	function _reply( $state, $text, $object )
	{
   	    $this->global_lock->release();
   	    
   	    parent::_reply( $state, $text, $object );
	}
}
