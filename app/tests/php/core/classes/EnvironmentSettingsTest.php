<?php

include_once SERVER_ROOT_PATH.'core/classes/system/EnvironmentSettings.php';

class EnvironmentSettingsTest extends PHPUnit_Framework_TestCase
{
	public function testServerPortDefault()
	{
	    $this->assertEquals( 80, EnvironmentSettings::getServerPort() );
	}

	public function testServerPortCustom()
	{
	    $class = $this->getMockClass("EnvironmentSettings", array("getCustomServerPort"));
	    
	    $class::staticExpects($this->any())->method('getCustomServerPort')->will($this->returnValue( 81 ));
	    
	    $this->assertEquals( 81, $class::getServerPort() );
	}

	public function testServerAddress()
	{
	    $this->assertEquals( '127.0.0.1', EnvironmentSettings::getServerAddress() );
	}

	public function testServerName()
	{
	    $this->assertEquals( gethostname(), EnvironmentSettings::getServerName() );
	}
	
	public function testServerSchemaHttp()
	{
	    $this->assertEquals( 'http', EnvironmentSettings::getServerSchema() );
	}

	public function testServerSchemaHttps()
	{
	    $class = $this->getMockClass("EnvironmentSettings", array("getHttps"));
	    
	    $class::staticExpects($this->any())->method('getHttps')->will($this->returnValue( true ));
	    
	    $this->assertEquals( 'https', $class::getServerSchema() );
	}

	public function testServerUrl()
	{
	    $this->assertEquals( 'http://'.gethostname(), EnvironmentSettings::getServerUrl() );
	}

	public function testServerUrlCustom()
	{
	    $class = $this->getMockClass("EnvironmentSettings", array("getHttps", "getCustomServerPort", "getCustomServerName"));
	    
	    $class::staticExpects($this->any())->method('getCustomServerPort')->will($this->returnValue( 66 ));
	    
	    $class::staticExpects($this->any())->method('getCustomServerName')->will($this->returnValue( "dummy-server-name" ));
	    
	    $class::staticExpects($this->any())->method('getHttps')->will($this->returnValue( true ));
	    
	    $this->assertEquals( 'https://dummy-server-name:66', $class::getServerUrl() );
	}
	
	public function testServerUrlByIpAddress()
	{
	    $this->assertEquals( 'http://'.gethostbyname(gethostname()), EnvironmentSettings::getServerUrlByIpAddress() );
	}

	public function testServerUrlByLocalhost()
	{
	    $this->assertEquals( 'http://127.0.0.1', EnvironmentSettings::getServerUrlLocalhost() );
	}

	public function testCustomTimeZone()
	{
		$tz_name = 'Asia/Dubai';
		
		EnvironmentSettings::setClientTimeZone($tz_name);
		
	    $this->assertEquals( $tz_name, EnvironmentSettings::getClientTimeZone()->getName() );
	}
}
