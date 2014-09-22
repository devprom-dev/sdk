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
    	if( $this->session->getProjectIt()->get('IsFileServer') != 'Y' ) return;
    	
    	$registry->addSection(getFactory()->getObject('pm_ArtefactType'), 'Folders', array(), true, text(935));
    }
}