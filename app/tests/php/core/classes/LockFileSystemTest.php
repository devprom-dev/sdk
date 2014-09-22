<?php

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class LockFileSystemTest extends PHPUnit_Framework_TestCase
{
	public function testLocked()
	{
	    $lock = $this->getMock('LockFileSystem', array('getLockTime'), array('-name'));

	    $lock->expects($this->any())->method('getLockTime')->will($this->returnValue( time() - 10 ));
	    
	    $this->assertTrue( $lock->Locked(15) );
	}

	public function testNoMoreLocked()
	{
	    $lock = $this->getMock('LockFileSystem', array('getLockTime'), array('-name'));
	    
	    $lock->expects($this->any())->method('getLockTime')->will($this->returnValue( time() - 10 ));
	    
	    $this->assertFalse( $lock->Locked(5) );
	}

	public function testUnlockedEmptyLock()
	{
	    $lock = $this->getMock('LockFileSystem', array('getLockTime'), array('-name'));
	    
	    $lock->expects($this->any())->method('getLockTime')->will($this->returnValue( '' ));
	    
	    $this->assertFalse( $lock->Locked(5) );
	}
}
