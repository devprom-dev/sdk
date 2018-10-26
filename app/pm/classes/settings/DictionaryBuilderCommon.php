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
 		$methodology_it = $this->session->getProjectIt()->getMethodologyIt();
		
 		$registry->addEntity( getFactory()->getObject('PMCustomAttribute') );
 		$registry->addEntity( getFactory()->getObject('RequestType'),
            $methodology_it->get('IsRequirements') == ReqManagementModeRegistry::RDD ? text(2672) : ''
        );
 		$registry->addEntity( getFactory()->getObject('RequestTemplate') );
        $registry->addEntity( getFactory()->getObject('TextTemplate') );
        $registry->addEntity( getFactory()->getObject('ExportTemplate') );

 	 	if ( $methodology_it->HasFeatures() ) {
 	 		$registry->addEntity( getFactory()->getObject('FeatureType'), text(1914) );
 	 	}
 		if ( $methodology_it->HasTasks() ) {
            $registry->addEntity( getFactory()->getObject('pm_TaskType') );
        }
	}
	
	private $session;
}