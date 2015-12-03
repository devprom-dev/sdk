<?php
include_once SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateArtefactsBuilderProduct extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;

	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
        $registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('FeatureType'));
        $registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('Feature'));
	}
}