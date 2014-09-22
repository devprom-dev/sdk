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
    	$validator = new ModelValidator();
    	
    	$data = array( 'RecordModified' => 'asd' );
    	
    	$this->assertEquals("RecordModified", $validator->validateType(new Snapshot(), $data)); 

    	$data = array( 'RecordModified' => '2013-32-43' );
    	
    	$this->assertEquals("RecordModified", $validator->validateType(new Snapshot(), $data)); 
    	
    	$data = array( 'RecordModified' => '2013-02-03' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 

    	$data = array( 'RecordModified' => '' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 
    }

    function testModelValidOnNumbers()
    {   
    	$validator = new ModelValidator();
    	
    	$data = array( 'OrderNum' => 'asd' );
    	
    	$this->assertEquals("OrderNum", $validator->validateType(new Snapshot(), $data)); 

    	$data = array( 'OrderNum' => '1' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 

    	$data = array( 'OrderNum' => '14645646456' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 

        $data = array( 'OrderNum' => '14645.646456' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 

        $data = array( 'OrderNum' => '14645,646456' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 
    }

    function testModelValidOnReferences()
    {   
    	$validator = new ModelValidator();
    	
    	$data = array( 'SystemUser' => 'asd' );

    	$this->assertEquals("SystemUser", $validator->validateType(new Snapshot(), $data)); 

        $data = array( 'SystemUser' => '14645.646456' );
    	
    	$this->assertEquals("SystemUser", $validator->validateType(new Snapshot(), $data)); 
    	
    	$data = array( 'SystemUser' => '14645646456' ); // more than integer
    	
    	$this->assertEquals("SystemUser", $validator->validateType(new Snapshot(), $data)); 
    	
    	$data = array( 'SystemUser' => '1' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 

        $data = array( 'SystemUser' => '14645,646456' );
    	
    	$this->assertEquals("", $validator->validateType(new Snapshot(), $data)); 
    }
    
    function testModelValidOnPasswords()
    {   
    	$validator = new ModelValidator();
    	
    	$data = array( 'Password' => 'asd' );
    	
    	$this->assertEquals("", $validator->validateType(new User(), $data)); 
    }
}