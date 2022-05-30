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
        $registry->addSectionItem('Dictionaries', getFactory()->getObject('FeatureType'));
        $feature = getFactory()->getObject('Feature');
        $feature->addSort( new SortObjectHierarchyClause() );
        $registry->addSectionItem('ProjectArtefacts', $feature);
	}
}