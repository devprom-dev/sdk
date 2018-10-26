<?php
include_once "WorkflowBuilder.php";

class WorkflowBuilderCommon extends WorkflowBuilder
{
	public function __construct( PMSession $session ) {
		$this->session = $session;
	}
	
	public function build( WorkflowRegistry & $registry )
	{
	 	$registry->addEntity( getFactory()->getObject('IssueState') );
 		$registry->addEntity( getFactory()->getObject('QuestionState') );
 		
 		$methodology_it = $this->session->getProjectIt()->getMethodologyIt();
 		if ( $methodology_it->HasTasks() ) {
	 		$registry->addEntity( getFactory()->getObject('TaskState') );
 		}
	}
	
	private $session;
}