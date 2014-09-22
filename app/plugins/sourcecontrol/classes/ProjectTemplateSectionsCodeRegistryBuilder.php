<?php

include_once SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateSectionsCodeRegistryBuilder extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
    	$artefact_objects = array (
				'pm_Subversion',
    			'pm_SubversionRevision',
				'pm_SubversionUser',
    			'RequestTraceSourceCode',
    			'TaskTraceSourceCode'
    	);
		
		foreach( $artefact_objects as $class_name )
		{
			$registry->addSectionItem('ProjectArtefacts', getFactory()->getObject($class_name));
		}
    }
}