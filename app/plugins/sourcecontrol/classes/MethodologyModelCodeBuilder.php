<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
		
class MethodologyModelCodeBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
    	if ( !$object instanceof Methodology ) return;
    	$object->addAttribute( 'IsSubversionUsed', 'CHAR', "text(sourcecontrol8)", true, true, "text(sourcecontrol7)" );
    }
}