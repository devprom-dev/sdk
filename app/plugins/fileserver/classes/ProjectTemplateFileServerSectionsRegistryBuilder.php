<?php

include_once SERVER_ROOT_PATH."pm/classes/project/ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateFileServerSectionsRegistryBuilder extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
    	$registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('ArtefactType'));
    	$registry->addSectionItem('ProjectArtefacts', getFactory()->getObject('Artefact'));
    }
}