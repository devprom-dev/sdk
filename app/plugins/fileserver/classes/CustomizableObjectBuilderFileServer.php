<?php

include_once SERVER_ROOT_PATH."pm/classes/common/CustomizableObjectBuilder.php";

class CustomizableObjectBuilderFileServer extends CustomizableObjectBuilder
{
	public function build( CustomizableObjectRegistry & $set )
    {
		if( $this->getSession()->getProjectIt()->getMethodologyIt()->get('IsFileServer') != 'Y' ) return;
        
        $set->add( 'Artefact' );
    }
}