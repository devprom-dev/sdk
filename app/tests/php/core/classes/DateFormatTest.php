<?php

include_once SERVER_ROOT_PATH.'lang/classes/DateFormatRussian.php';
include_once SERVER_ROOT_PATH.'lang/classes/DateFormatEuropean.php';
include_once SERVER_ROOT_PATH.'lang/classes/DateFormatAmerican.php';

class DateFormatTest extends PHPUnit_Framework_TestCase
{
	public function testEuropeanFormatter()
	{
		$formatter = new DateFormatEuropean();
		
		$this->assertEquals( '', $formatter->getDbDate('00/00/0000') );

		$this->assertEquals( '', $formatter->getDbDate('31.12.1999') );

		$this->assertEquals( '', $formatter->getDbDate('1999-31-12') );
		
		$this->assertEquals( '', $formatter->getDbDate('05/19/2013') );
		
		$this->assertEquals( '2013-05-19', $formatter->getDbDate('19/05/2013') );
	}

	public function testRussianFormatter()
	{
		$formatter = new DateFormatRussian();
		
		$this->assertEquals( '', $formatter->getDbDate('00.00.0000') );

		$this->assertEquals( '', $formatter->getDbDate('12.31.1999') );

		$this->assertEquals( '2013-05-19', $formatter->getDbDate('19.05.2013') );
	}

	public function testAmericanFormatter()
	{
		$formatter = new DateFormatAmerican();
		
		$this->assertEquals( '', $formatter->getDbDate('00/00/0000') );

		$this->assertEquals( '', $formatter->getDbDate('31.12.1999') );

		$this->assertEquals( '', $formatter->getDbDate('1999-31-12') );
		
		$this->assertEquals( '', $formatter->getDbDate('19/05/2013') );
		
		$this->assertEquals( '2013-05-19', $formatter->getDbDate('05/19/2013') );
	}
}
