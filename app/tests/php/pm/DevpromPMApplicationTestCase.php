<?php

include_once SERVER_ROOT_PATH."core/classes/project/Project.php";
include_once SERVER_ROOT_PATH."core/classes/project/ProjectImportance.php";
include_once SERVER_ROOT_PATH."pm/classes/sessions/PMSession.php";

abstract class DevpromPMApplicationTestCase extends DevpromTestCase
{
    protected $project_mock;
    protected $participant_mock;
    
    function setUp()
    {
        global $session;

        parent::setUp();

        $this->workflowMock =
            $this->getMockBuilder(WorkflowScheme::class)
                ->disableOriginalConstructor()
                ->setMethods(["buildScheme","getStates"])
                ->getMock();

        $ref = new \ReflectionProperty('WorkflowScheme', 'singleInstance');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->workflowMock);

        // project mock
        $this->project_mock =
            $this->getMockBuilder(Project::class)
                ->disableOriginalConstructor()
                ->setMethods(["createIterator"])
                ->getMock();

        $project_iterator =
            $this->getMockBuilder(ProjectIterator::class)
                ->setConstructorArgs(array($this->project_mock))
                ->setMethods(["getMethodologyIt"])
                ->getMock();

        $project_iterator->expects($this->any())->method('getMethodologyIt')->will( 
                $this->returnValue( $this->getMethodologyIt() 
                ));
        
        $this->project_mock->expects($this->any())->method('createIterator')->will( 
                $this->returnValue($project_iterator
                ));

        // participant mock
        $this->participant_mock =
            $this->getMockBuilder(Participant::class)
                ->disableOriginalConstructor()
                ->setMethods(["createIterator"])
                ->getMock();

        $this->participant_mock->expects($this->any())->method('createIterator')->will( 
                $this->returnValue( new ParticipantIterator($this->participant_mock) 
                ));

        // other mocks
        $auth_factory_mock =
            $this->getMockBuilder(AuthenticationFactory::class)
                ->disableOriginalConstructor()
                ->setMethods(["authorize"])
                ->getMock();

        $user = new User();

        $auth_factory_mock->expects($this->any())->method('authorize')
            ->will($this->returnValue( $user->createCachedIterator( array( array(
        			'cms_UserId' => 1,
        			'IsAdministrator' => 'Y' 
        		)))
            ));

        $session_mock =
            $this->getMockBuilder(PMSession::class)
                ->setConstructorArgs(array($this->getProjectIt(), $auth_factory_mock))
                ->setMethods(['configure','getBuilders','getProjectIt','getUserIt','getParticipantIt'])
                ->getMock();

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
			array( 'ObjectModelBuilder', $this->getModelBuilders() ),
    		array( 'EstimationStrategyBuilder', array() )
        );
    }
    
    function getMetadataBuilders()
    {
        return array(
            new ObjectMetadataModelBuilder()
        );
    }

    function getModelBuilders() {
        return array();
    }

    abstract public function getMethodologyIt();
}
