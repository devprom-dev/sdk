<?php

include_once SERVER_ROOT_PATH."pm/classes/issues/Request.php";
include_once SERVER_ROOT_PATH."pm/classes/issues/IssueState.php";
include_once SERVER_ROOT_PATH."pm/classes/tasks/Task.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/Release.php";
include_once SERVER_ROOT_PATH."pm/classes/plan/Stage.php";
include_once SERVER_ROOT_PATH."tests/php/pm/DevpromSDLCTestCase.php";

class RequestSDLCModelTest extends DevpromSDLCTestCase
{
    function getMetadataBuilders()
    {
        return array_merge( parent::getMetadataBuilders(), array(
            new RequestMetadataBuilder()
        ));
    }
    
    function setUp()
    {
        global $model_factory;
        
        parent::setUp();
        
        // entity mocks

        $entity = $this->getMockBuilder(Request::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact','moveToState','getStates'])
            ->getMock();

        $entity->expects($this->any())->method('getStates')->will( $this->returnValue(
            array (
                'submitted',
                'resolved'
            )
        ));
        $entity->expects($this->any())->method('getExact')->will( $this->returnValueMap(
                array (
                        array ( '1', $entity->createCachedIterator(array(
                            array( 
                                    'pm_ChangeRequestId' => '1', 
                                    'Caption' => 'FirstRequest', 
                                    'PlannedRelease' => '1' 
                                 )
                        )))
                ) 
        ));

        $task = $this->getMockBuilder(Task::class)
            ->setConstructorArgs(array())
            ->setMethods(['createSQLIterator'])
            ->getMock();

        $task->expects($this->any())->method('createSQLIterator')->will( $this->returnValue(
                 $task->createCachedIterator(array (
                         array (
                                 'pm_TaskId' => '1',
                                 'ChangeRequest' => '1'
                               )
                 ))
        ));

        $release = $this->getMockBuilder(Release::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact'])
            ->getMock();

        $release->expects($this->any())->method('getExact')->will( $this->returnValue(
                 $release->createCachedIterator(array (
                         array (
                                 'pm_VersionId' => '1',
                                 'Caption' => '1.2.3'
                               )
                 ))
        ));
        
        $stage = $this->getMockBuilder(Stage::class)
            ->setConstructorArgs(array())
            ->setMethods(['getExact'])
            ->getMock();

        $stage->expects($this->any())->method('getExact')->will( $this->returnValue(
                 $stage->createCachedIterator(array (
                         array ()
                 ))
        ));
        
        $model_factory->expects($this->any())->method('createInstance')->will( $this->returnValueMap(
                array (
                        array ( 'Request', null, $entity ),
                        array ( 'Task', null, $task ),
                        array ( 'Release', null, $release ),
                        array ( 'Stage', null, $stage )
                ) 
        ));
    }

    function testDummy() {
        $this->assertTrue(true);
    }
}