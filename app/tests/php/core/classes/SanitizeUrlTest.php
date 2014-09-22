<?php

include_once SERVER_ROOT_PATH.'core/classes/system/SanitizeUrl.php';

class SanitizeUrlTest extends PHPUnit_Framework_TestCase
{
	private $url_with_tags = '/pm/tyjtjty/module/helpdocs/list/wj3aeqd9.asp?<script>document.cookie=%22testqrgv=4463;%22</script>';
	 
	private $malicious_urls = array (
				'msgbox("foo");window.alert(\'bar\');',
				'/pm/509%22%20src=%22http://www.example.com/exploit509.js',
				'/site/reboot/me'
			);
	
	private $valid_urls = array (
				'http://devprom/pm/my?list=my',
				'/admin/updates.php?parms=test',
				'http://devprom/pm/my/'
			);
	
	public function testMaliciousUrls()
	{
		foreach( $this->malicious_urls as $url )
		{
			$this->assertEquals( '', SanitizeUrl::parseSystemUrl($url) );
		}
	}

	public function testValidUrls()
	{
		foreach( $this->valid_urls as $url )
		{
			$parts = parse_url($url);
			
			$this->assertEquals( $parts['path'].($parts['query'] != '' ? '?'.$parts['query'] : ""), SanitizeUrl::parseSystemUrl($url) );
		}
	}
	
	public function testPathOnlyUrls()
	{
	    $this->assertEquals( 
	    		EnvironmentSettings::getServerUrl().'/pm/tyjtjty/module/helpdocs/list/wj3aeqd9.asp', 
	    		SanitizeUrl::parseUrlSkipQueryString($this->url_with_tags)
		);
	}

	public function testUrlsStripTags()
	{
	    $this->assertEquals( 
	    		'/pm/tyjtjty/module/helpdocs/list/wj3aeqd9.asp?document.cookie="testqrgv=4463;"', 
	    		SanitizeUrl::parseUrl($this->url_with_tags)
		);
	}
}
