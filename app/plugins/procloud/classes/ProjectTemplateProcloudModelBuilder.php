<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include "ProjectTemplateRegistryProcloud.php";
		
class ProjectTemplateProcloudModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ProjectTemplate' ) return;
    	
    	$object->setRegistry( new ProjectTemplateRegistryProcloud($object) );
    }
}