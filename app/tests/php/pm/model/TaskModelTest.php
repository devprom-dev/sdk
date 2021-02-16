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
        
        $this->entity = $this->getMockBuilder(Task::class)
            ->setConstructorArgs(array())
            ->setMethods(['getTerminalStates','getNonTerminalStates'])
            ->getMock();

        getFactory()->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Task', null, $this->entity ),
                        array ( 'TaskState', null, new TaskState )
                ) 
        ));
        $this->entity->expects($this->any())->method('getTerminalStates')->will(
            $this->returnValue(array('closed'))
        );
        $this->entity->expects($this->any())->method('getNonTerminalStates')->will(
            $this->returnValue(array('new','active'))
        );
    }

    function testFinishDateChanged()
    {
        $this->getDALMock()->expects($this->at(1))->method('Query')->with(
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
        $this->getDALMock()->expects($this->at(1))->method('Query')->with(
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