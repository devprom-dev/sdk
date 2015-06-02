<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";

include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/IssueState.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetWorkItemDatesTrigger.php"; 

class RequestModelTest extends DevpromDummyTestCase
{
    protected $entity;
    
    function setUp()
    {
        parent::setUp();
        
        // entity mocks
        
        $this->entity = $this->getMock('Request', array('cacheStates'));
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Request', null, $this->entity ),
                        array ( 'IssueState', null, new IssueState ),
                        array ( 'RequestTraceMilestone', null, new RequestTraceMilestone )
                ) 
        ));
        
        $this->entity->expects($this->any())->method('cacheStates')->will( 
        		$this->returnValue(
	                getFactory()->getObject('IssueState')->createCachedIterator(
	                		array(
	                			array (
	                					'pm_StateId' => 1,
	                					'ReferenceName' => 'new',
	                					'IsTerminal' => 'N'
	                			),
	                			array (
	                					'pm_StateId' => 1,
	                					'ReferenceName' => 'active',
	                					'IsTerminal' => 'N'
	                			),
	                			array (
	                					'pm_StateId' => 2,
	                					'ReferenceName' => 'closed',
	                					'IsTerminal' => 'Y'
	                			)
	                		)
	        		) 
        		)
	    );
    }

    function testFinishDateChanged()
    {
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->stringContains("FinishDate = NOW()")
        );

        $trigger = new SetWorkItemDatesTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3', 
                                    'State' => 'new' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3', 
                                    'State' => 'closed' 
                                 )
                        ))
        );
    }

    function testFinishDateEmpty()
    {
        $this->getDALMock()->expects($this->at(1))->method('Query')->with(
                $this->stringContains("FinishDate = NULL")
        );
    	
        $trigger = new SetWorkItemDatesTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3',
                            		'Estimation' => '1', 
                                    'State' => 'new' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3', 
                            		'Estimation' => '5', 
                            		'State' => 'active' 
                                 )
                        ))
        );
    }
    
    function testFinishDateUnchanged()
    {
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->logicalNot($this->stringContains("FinishDate = "))
        );
    	
        $trigger = new SetWorkItemDatesTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3',
                            		'Estimation' => '1', 
                                    'State' => 'closed' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3', 
                            		'Estimation' => '5', 
                            		'State' => 'closed' 
                                 )
                        ))
        );
    }

    function testFinishDateReset()
    {
        $this->getDALMock()->expects($this->at(0))->method('Query')->with(
                $this->stringContains("FinishDate = NULL")
        );

        $trigger = new SetWorkItemDatesTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3',
                            		'Estimation' => '1', 
                                    'State' => 'closed' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '3', 
                            		'Estimation' => '5', 
                            		'State' => 'new' 
                                 )
                        ))
        );
    }
}