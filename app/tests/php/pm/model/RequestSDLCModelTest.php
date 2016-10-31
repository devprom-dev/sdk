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

        $entity = $this->getMock('Request', array('getExact','moveToState','getStates'));
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

        $task = $this->getMock('Task', array('createSQLIterator'));
        
        $task->expects($this->any())->method('createSQLIterator')->will( $this->returnValue(
                 $task->createCachedIterator(array (
                         array (
                                 'pm_TaskId' => '1',
                                 'ChangeRequest' => '1'
                               )
                 ))
        ));

        $release = $this->getMock('Release', array('getExact'));
        
        $release->expects($this->any())->method('getExact')->will( $this->returnValue(
                 $release->createCachedIterator(array (
                         array (
                                 'pm_VersionId' => '1',
                                 'Caption' => '1.2.3'
                               )
                 ))
        ));
        
        $stage = $this->getMock('Stage', array('getExact'));
        
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
    
    function testRequestUpdateClosedInVersionByPlannedRelease()
    {
        global $model_factory;
        
        $this->getDALMock()->expects($this->atLeastOnce())->method('Query')->with(
                $this->logicalOr(
                    $this->stringContains('SELECT'),
                    $this->logicalAnd(
                        $this->stringContains('UPDATE'),
                        $this->stringContains("`ClosedInVersion` = '1.2.3'")
                       )
                    )
                );

        $object = $model_factory->getObject('Request');
        
        $object->modify_parms( '1', array( 'State' => 'resolved' ) );
    }
}