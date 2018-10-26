<?php
include_once "ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateArtefactsBuilderWorkItems extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session ) {
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
		$registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('Request'));
		$registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('Task'));
	}
}