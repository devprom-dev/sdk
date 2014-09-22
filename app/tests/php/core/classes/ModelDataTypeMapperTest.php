<?php

include_once SERVER_ROOT_PATH."tests/php/DevpromTestCase.php";
include_once SERVER_ROOT_PATH."core/classes/versioning/Snapshot.php";
include_once SERVER_ROOT_PATH."pm/classes/time/Activity.php";
include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include_once SERVER_ROOT_PATH."core/classes/model/mappers/ModelDataTypeMapper.php";
include_once SERVER_ROOT_PATH."lang/classes/c_language.php";

class ModelDataTypeMapperTest extends DevpromTestCase
{
    function setUp()
    {
        parent::setUp();
    }

    function testModelMapperOnDateTimes()
    {   
    	$mapper = new ModelDataTypeMapper();
    	
    	$data = array( 'RecordModified' => '31.10.2013' );
    	
    	$mapper->map(new Snapshot(), $data);
    	
    	$this->assertEquals(SystemDateTime::convertToServerTime("2013-10-31"), $data['RecordModified']); 
    }

    function testModelMapperOnDates()
    {   
    	$mapper = new ModelDataTypeMapper();
    	
    	$data = array( 'ReportDate' => '31.10.2013' );
    	
    	$mapper->map(new Activity(), $data);
    	
    	$this->assertEquals("2013-10-31", $data['ReportDate']); 
    }
}