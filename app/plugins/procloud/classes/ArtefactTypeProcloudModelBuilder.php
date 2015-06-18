<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class ArtefactTypeProcloudModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_ArtefactType' ) return;
    	
 		$object->addAttribute( 'IsDisplayedOnSite', 'CHAR', "text(procloud943)", true, true, "text(procloud476)" );
    }
}