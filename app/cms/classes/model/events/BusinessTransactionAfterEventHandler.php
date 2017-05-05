<?php

abstract class BusinessTransactionAfterEventHandler
{
	abstract protected function handle( $object_it );
	
	public function setObjectIt( $object_it )
	{
		$this->object_it = $object_it;
	}
	
	public function getObjectIt()
	{
		return $this->object_it;
	}

	public function readyToHandle()
	{
		return true;
	}
	
	public function process( $data )
	{
        $this->data = $data;
		$object_it = $this->getObjectIt();
		
		while( !$object_it->end() ) {
			$this->handle( $object_it->copy() );
			$object_it->moveNext();
		}
	}

	protected function getData() {
        return $this->data;
    }
	
	public function __destruct()
	{
	    $lock = new LockFileSystem( get_class($this->object_it->object) );
	    $lock->Release();
	}
	
	private $object_it;
    private $data = array();
}