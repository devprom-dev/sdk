<?php

use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestType.php";

class ModelServiceTest extends DevpromDummyTestCase
{
    protected $entity;
    
    function setUp()
    {
        parent::setUp();
        
        // entity mocks
        
        $this->entity = $this->getMock('RequestType', array('getAll'));
        $this->entity->expects($this->any())->method('getAll')->will( 
        		$this->returnValue(
	                $this->entity->createCachedIterator(
	                		array(
	                			array (
	                					'pm_IssueTypeId' => 1,
	                					'Caption' => 'Test 1',
	                					'ReferenceName' => 'Height',
										'OrderNum' => 1
	                			),
	                			array (
	                					'pm_IssueTypeId' => 2,
	                					'Caption' => 'Test 2',
	                					'ReferenceName' => 'Low',
										'OrderNum' => 2
	                			)
	                		)
	        		) 
        		)
	    );
    }

    function testXPathQuery()
    {
    	$it = $this->entity->getAll();
    	$this->assertEquals(2, ModelService::queryXPath($it, 'contains(Caption,"test")')->count());
    	
    	$it->moveFirst();
    	$this->assertEquals(1, ModelService::queryXPath($it, 'Caption="test 1"')->count());
    	
    	$it->moveFirst();
    	$this->assertEquals(1, ModelService::queryXPath($it, 'contains(ReferenceName,"low")')->count());

		$it->moveFirst();
		$this->assertEquals(2, ModelService::queryXPath($it, 'OrderNum>"0"')->count());

		$it->moveFirst();
		$this->assertEquals(1, ModelService::queryXPath($it, 'OrderNum<"2"')->count());
	}
}