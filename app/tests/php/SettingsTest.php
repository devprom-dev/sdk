<?php

class SettingsTest extends \PHPUnit\Framework\TestCase
{
	public function setUp()
	{
	}
	
	public function testServerRootPathConstant()
	{
	    $this->assertTrue( defined("SERVER_ROOT_PATH") );
	    
	    $this->assertTrue( is_dir(SERVER_ROOT_PATH) );
	}
}
