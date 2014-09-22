<?php

include SERVER_ROOT_PATH.'api/classes/SoapService.php';

class SoapServiceTest extends DevpromTestCase
{
    function testConvertionValuesSoapToSystem()
    {   
        $service = $this->getMock('SoapService', array('login'));
        
        $date_value = "2011-01-10";
        
        $this->assertEquals( "2011-01-10", $service->soapValueToSystem("xsd:date", $date_value ));
        
        $datetime_value = "2011-01-10T21:57:59";
        
        $this->assertEquals( "2011-01-10 21:57:59", $service->soapValueToSystem("xsd:dateTime", $datetime_value ));
        $this->assertEquals( "2011-01-10", $service->soapValueToSystem("xsd:dateTime", $date_value ));
    }
}