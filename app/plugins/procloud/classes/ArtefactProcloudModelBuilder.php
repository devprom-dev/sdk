<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class ArtefactProcloudModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( $object->getEntityRefName() != 'pm_Artefact' ) return;
    	
    	$object->addAttribute( 'IsAuthorizedDownload', 'CHAR', "text(procloud942)", true, true, "text(procloud523)" );
    }
}