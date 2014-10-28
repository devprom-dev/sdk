<?php

include_once "ProjectTemplateSectionsRegistryBuilder.php";

class ProjectTemplateSectionsRegistryBuilderLatest extends ProjectTemplateSectionsRegistryBuilder
{
	private $session = null;
	
	public function __construct( PMSession $session )
	{
		$this->session = $session;
	}
	
    public function build ( ProjectTemplateSectionsRegistry & $registry )
    {
 		$items = array (
 			getFactory()->getObject('WikiPageChange')
 		);

    	foreach( $items as $object )
		{
			$registry->addSectionItem('ProjectArtefacts', $object);
		}
    }
}