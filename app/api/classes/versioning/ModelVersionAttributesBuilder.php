<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."core/classes/versioning/VersionedObject.php";
include_once "persisters/DataModelVersionPersister.php";

class ModelVersionAttributesBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'WikiPage' ) return;
    	
    	$object->addAttribute('Version', 'INTEGER', translate('������'), true);
    	
    	$object->addAttribute('VersionName', 'TEXT', translate('������'), true);
    	
    	$object->addPersister( new DataModelVersionPersister() );
    }
}