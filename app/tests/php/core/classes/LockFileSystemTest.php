<?php

include_once SERVER_ROOT_PATH.'core/classes/system/LockFileSystem.php';

class LockFileSystemTest extends \PHPUnit\Framework\TestCase
{
    private $fsLock = null;

    function setUp() {
        parent::setUp();

        $this->fsLock = $this->getMockBuilder(\LockFileSystem::class)
            ->setConstructorArgs(array('-name'))
            ->setMethods(['getLockTime'])
            ->getMock();
    }
	public function testLocked()
	{
        $this->fsLock->expects($this->any())->method('getLockTime')->will($this->returnValue( time() - 10 ));
	    $this->assertTrue( $this->fsLock->Locked(15) );
	}

	public function testNoMoreLocked()
	{
        $this->fsLock->expects($this->any())->method('getLockTime')->will($this->returnValue( time() - 10 ));
	    $this->assertFalse( $this->fsLock->Locked(5) );
	}

	public function testUnlockedEmptyLock()
	{
        $this->fsLock->expects($this->any())->method('getLockTime')->will($this->returnValue( '' ));
	    $this->assertFalse( $this->fsLock->Locked(5) );
	}
}
