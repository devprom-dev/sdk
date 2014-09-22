<?php

include_once SERVER_ROOT_PATH."tests/php/pm/DevpromDummyTestCase.php";

include_once SERVER_ROOT_PATH."pm/classes/tasks/Task.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskState.php";
include_once SERVER_ROOT_PATH."pm/classes/model/events/SetWorkItemDatesTrigger.php"; 

class TaskModelTest extends DevpromDummyTestCase
{
    protected $entity;
    
    function setUp()
    {
        parent::setUp();
        
        // entity mocks
        
        $this->entity = $this->getMock('Task', array('cacheStates'));
        
        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Task', null, $this->entity ),
                        array ( 'TaskState', null, new TaskState )
                ) 
        ));
        
        $this->entity->expects($this->any())->method('cacheStates')->will( 
        		$this->returnValue(
	                getFactory()->getObject('TaskState')->createCachedIterator(
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
                                    'pm_TaskId' => '3', 
                                    'State' => 'new' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_TaskId' => '3', 
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
                                    'pm_TaskId' => '3',
                            		'Planned' => '1', 
                                    'State' => 'new' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_TaskId' => '3', 
                            		'Planned' => '5', 
                            		'State' => 'active' 
                                 )
                        ))
        );
    }
    
    function testFinishDateUnchanged()
    {
        $this->getDALMock()->expects($this->never())->method('Query');

        $trigger = new SetWorkItemDatesTrigger();

        $trigger->modify( 
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_TaskId' => '3',
                            		'Planned' => '1', 
                                    'State' => 'closed' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_TaskId' => '3', 
                            		'Planned' => '5', 
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
                                    'pm_TaskId' => '3',
                            		'Planned' => '1', 
                                    'State' => 'closed' 
                                 )
                        )),
                $this->entity->createCachedIterator(array(
                            array( 
                                    'pm_TaskId' => '3', 
                            		'Planned' => '5', 
                            		'State' => 'new' 
                                 )
                        ))
        );
    }
}