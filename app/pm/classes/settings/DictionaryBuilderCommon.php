<?php

include_once "DictionaryBuilder.php";

class DictionaryBuilderCommon extends DictionaryBuilder
{
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
	public function build( DictionaryRegistry & $registry )
	{
 		$registry->addEntity( getFactory()->getObject('PMCustomAttribute') );
		
 		$registry->addEntity( getFactory()->getObject('CustomResource') );
 		
 		$registry->addEntity( getFactory()->getObject('pm_ProjectRole') );

 		$registry->addEntity( getFactory()->getObject('pm_IssueType') );
 		
 		$registry->addEntity( getFactory()->getObject('RequestTemplate') );

 		$methodology_it = $this->session->getProjectIt()->getMethodologyIt();
 		
 		if ( $methodology_it->HasPlanning() ) 
 		{
 			$registry->addEntity( getFactory()->getObject('pm_ProjectStage') );
 		}
 		
 		if ( $methodology_it->HasTasks() )
 		{
 			$registry->addEntity( getFactory()->getObject('pm_TaskType') );
 		}
	}
	
	private $session;
}