<?php

include_once SERVER_ROOT_PATH."tests/php/DevpromTestCase.php";
include_once SERVER_ROOT_PATH."core/classes/versioning/Snapshot.php";
include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidator.php";

class ModelValidatorTest extends DevpromTestCase
{
    function setUp()
    {
        parent::setUp();
    }

    function testModelValidOnDates()
    {   
    	$validator = new ModelValidator(array (
								new \ModelValidatorTypes()
    					));
    	
    	$data = array( 'RecordModified' => 'asd' );
    	
    	$this->assertNotEquals("", $validator->validate(new Snapshot(), $data)); 

    	$data = array( 'RecordModified' => '2013-32-43' );
    	
    	$this->assertNotEquals("", $validator->validate(new Snapshot(), $data)); 
    	
    	$data = array( 'RecordModified' => '2013-02-03' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 

    	$data = array( 'RecordModified' => "" );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 
    }

    function testModelValidOnNumbers()
    {   
    	$validator = new ModelValidator(array (
								new \ModelValidatorTypes()
    					));
    	
    	$data = array( 'OrderNum' => 'asd' );
    	
    	$this->assertNotEquals("", $validator->validate(new Snapshot(), $data)); 

    	$data = array( 'OrderNum' => '1' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 

    	$data = array( 'OrderNum' => '14645646456' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 

        $data = array( 'OrderNum' => '14645.646456' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 

        $data = array( 'OrderNum' => '14645,646456' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 
    }

    function testModelValidOnReferences()
    {   
    	$validator = new ModelValidator(array (
								new \ModelValidatorTypes()
    					));
    	
    	$data = array( 'SystemUser' => '1' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 
	
        $data = array( 'SystemUser' => '14645,646456' );
    	
    	$this->assertEquals("", $validator->validate(new Snapshot(), $data)); 
    }
    
    function testModelValidOnPasswords()
    {   
    	$validator = new ModelValidator(array (
								new \ModelValidatorTypes()
    					));
    	
    	$data = array( 'Password' => 'asd' );
    	
    	$this->assertEquals("", $validator->validate	(new User(), $data)); 
    }
}