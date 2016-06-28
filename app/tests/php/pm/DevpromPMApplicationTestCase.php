<?php

include_once SERVER_ROOT_PATH."core/classes/user/User.php";
include_once SERVER_ROOT_PATH."pm/classes/participants/Participant.php";
include_once SERVER_ROOT_PATH."core/classes/project/Project.php";
include_once SERVER_ROOT_PATH."core/classes/project/ProjectImportance.php";
include_once SERVER_ROOT_PATH."pm/classes/settings/Methodology.php";
include_once SERVER_ROOT_PATH."pm/classes/sessions/PMSession.php";

abstract class DevpromPMApplicationTestCase extends DevpromTestCase
{
    protected $project_mock;
    
    protected $participant_mock;
    
    function setUp()
    {
        global $model_factory, $session;

        parent::setUp();

        $this->workflowMock = $this->getMock('WorkflowScheme', array('buildScheme','getStates'), array(), '', false);
        $ref = new \ReflectionProperty('WorkflowScheme', 'singleInstance');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->workflowMock);

        // project mock
        $this->project_mock = $this->getMock('Project', array('createIterator'), array(), '', false);
        
        $project_iterator = $this->getMock('ProjectIterator', array('getMethodologyIt'), array($this->project_mock));
         
        $project_iterator->expects($this->any())->method('getMethodologyIt')->will( 
                $this->returnValue( $this->getMethodologyIt() 
                ));
        
        $this->project_mock->expects($this->any())->method('createIterator')->will( 
                $this->returnValue($project_iterator
                ));

        // participant mock
        $this->participant_mock = $this->getMock('Participant', array('createIterator') ); 

        $this->participant_mock->expects($this->any())->method('createIterator')->will( 
                $this->returnValue( new ParticipantIterator($this->participant_mock) 
                ));

        // other mocks
        $auth_factory_mock = $this->getMock('AuthenticationFactory', array('authorize'));

        $user = new User();
        
        $auth_factory_mock->expects($this->any())->method('authorize')
            ->will($this->returnValue( $user->createCachedIterator( array( array(
        			'cms_UserId' => 1,
        			'IsAdministrator' => 'Y' 
        		)))
            ));

        $session_mock = $this->getMock('PMSession',
            array('configure','getBuilders','getProjectIt','getUserIt','getParticipantIt'),
            array(
                $this->getProjectIt(),
                $auth_factory_mock
            )
        );

        $session_mock->expects($this->any())->method('getBuilders')
            ->will( $this->returnValueMap( $this->getBuilders() ));
        
        $session_mock->expects($this->any())->method('getProjectIt')
            ->will( $this->returnValue( $this->getProjectIt() ) );

        $session_mock->expects($this->any())->method('getUserIt')
            ->will( $this->returnValue( $auth_factory_mock->authorize() ) );
        
        $session_mock->expects($this->any())->method('getParticipantIt')
            ->will( $this->returnValue( $this->getParticipantIt() ) );
        
        $session = $session_mock;
    }
    
    function getProjectIt()
    {
        return $this->project_mock->createCachedIterator(array(
            array( 'pm_ProjectId' => '1' )
        ));
    }

    function getParticipantIt()
    {
        return $this->participant_mock->createCachedIterator(array(
            array( 'pm_ParticipantId' => '1' )
        ));
    }
    
    function getBuilders()
    {
    	return array (
			array( 'ObjectMetadataBuilder', $this->getMetadataBuilders() ),
			array( 'ObjectModelBuilder', array() ),
    		array( 'EstimationStrategyBuilder', array() )
        );
    }
    
    function getMetadataBuilders()
    {
        return array(
            new ObjectMetadataModelBuilder()
        );
    }

    abstract public function getMethodologyIt();
}
