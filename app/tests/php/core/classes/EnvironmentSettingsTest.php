<?php

include_once SERVER_ROOT_PATH.'core/classes/system/EnvironmentSettings.php';

class EnvironmentSettingsTest extends \PHPUnit\Framework\TestCase
{
	public function testServerPortDefault()
	{
	    $this->assertEquals( 80, EnvironmentSettings::getServerPort() );
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

	public function testServerUrl()
	{
	    $this->assertEquals( 'http://'.gethostname(), EnvironmentSettings::getServerUrl() );
	}

	public function testServerUrlByIpAddress()
	{
	    $this->assertEquals( 'http://'.gethostbyname(gethostname()), EnvironmentSettings::getServerUrlByIpAddress() );
	}

	public function testServerUrlByLocalhost()
	{
	    $this->assertEquals( 'http://127.0.0.1:80', EnvironmentSettings::getServerUrlLocalhost() );
	}

	public function testCustomTimeZone()
	{
		$tz_name = 'Asia/Dubai';
		
		EnvironmentSettings::setClientTimeZone($tz_name);
		
	    $this->assertEquals( $tz_name, EnvironmentSettings::getClientTimeZone()->getName() );
	}
}
