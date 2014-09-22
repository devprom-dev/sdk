<?php

class JsonWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testJsonEncodeString()
	{
	    $this->assertEquals('"data"', JsonWrapper::encode("data"));
	}

	public function testJsonEncodeSimpleArray()
	{
	    $this->assertEquals('{"data":"value"}', JsonWrapper::encode(array("data" => "value")));
	}
}
