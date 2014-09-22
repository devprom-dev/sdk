<?php

include_once "WorkflowBuilder.php";

class WorkflowBuilderCommon extends WorkflowBuilder
{
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
	public function build( WorkflowRegistry & $registry )
	{
		global $model_factory;
		
	 	$registry->addEntity( $model_factory->getObject('IssueState') );
	 	
 		$registry->addEntity( $model_factory->getObject('QuestionState') );
 		
 		$methodology_it = $this->session->getProjectIt()->getMethodologyIt();
 		
 		if ( $methodology_it->HasTasks() )
 		{
	 		$registry->addEntity( $model_factory->getObject('TaskState') );
 		}
	}
	
	private $session;
}