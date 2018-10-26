<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class MethodologyFilesModelBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Methodology ) return;
    	$object->addAttribute( 'IsFileServer', 'CHAR', "text(fileserver2)", true, true, "text(fileserver3)" );
    }
}