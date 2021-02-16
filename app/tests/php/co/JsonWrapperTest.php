<?php

class JsonWrapperTest extends \PHPUnit\Framework\TestCase
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
