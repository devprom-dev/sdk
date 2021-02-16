<?php

use Devprom\ProjectBundle\Service\Model\ModelService;
include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/RequestType.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/IssueAuthor.php";

class ModelServiceTest extends DevpromDummyTestCase
{
    protected $entity;
    
    function setUp()
    {
        parent::setUp();

        // entity mocks
        $this->entity = $this->getMockBuilder(RequestType::class)
            ->setConstructorArgs(array())
            ->setMethods(['getAll'])
            ->getMock();

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

        $this->issueAuthor = $this->getMockBuilder(IssueAuthor::class)
            ->setConstructorArgs(array())
            ->setMethods(['getRegistry'])
            ->getMock();

        $issueAuthorRegistry = $this->getMockBuilder(ObjectRegistrySQL::class)
            ->setConstructorArgs(array($this->issueAuthor))
            ->setMethods(['QueryById'])
            ->getMock();

        $this->issueAuthor->expects($this->any())->method('getRegistry')
            ->will($this->returnValue($issueAuthorRegistry));

        $issueAuthorRegistry->expects($this->any())->method('QueryById')->will(
            $this->returnValueMap(
                array(
                    array(1, $this->issueAuthor->createCachedIterator(
                        array(
                            array (
                                'cms_UserId' => 1,
                                'Caption' => 'Петрова Маргарита Сергеевна'
                            ),
                        )
                    )),
                    array('cabinet@nch-spb.ru', $this->issueAuthor->createCachedIterator(
                        array(
                            array (
                                'cms_UserId' => 2,
                                'Caption' => 'cabinet@nch-spb.ru'
                            ),
                        )
                    )),
                    array('', $this->issueAuthor->getEmptyIterator())
                )
            )
        );

        $this->request = $this->getMockBuilder(Request::class)
            ->setConstructorArgs(array())
            ->setMethods(['getAll'])
            ->getMock();

        $this->request->expects($this->any())->method('getAll')->will(
            $this->returnValue(
                $this->request->createCachedIterator(
                    array(
                        array (
                            'pm_ChangeRequestId' => 1,
                            'Caption' => 'Test 1',
                            'Author' => 1
                        ),
                        array (
                            'pm_ChangeRequestId' => 2,
                            'Caption' => 'Test 2',
                            'Author' => 'cabinet@nch-spb.ru'
                        ),
                        array (
                            'pm_ChangeRequestId' => 3,
                            'Caption' => 'Test 3',
                            'Author' => ''
                        )
                    )
                )
            )
        );

        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
            array (
                array ( 'User', null, $this->issueAuthor )
            )
        ));
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

    function testXPathQueryRequestAuthorRef()
    {
        $it = $this->request->getAll();
        $this->assertEquals(1, ModelService::queryXPath($it,
            'contains(Author,"'.addslashes(mb_strtolower("Маргарита")).'")')->count());
    }

    function testXPathQueryRequestAuthorEmail()
    {
        $it = $this->request->getAll();
        $this->assertEquals(1, ModelService::queryXPath($it,
            'contains(Author,"'.addslashes(mb_strtolower("cabinet@nch-spb.ru")).'")')->count());
    }
}