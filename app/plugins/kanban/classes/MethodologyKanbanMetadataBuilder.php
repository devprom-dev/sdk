<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class MethodologyKanbanMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Methodology' ) return;

    	$metadata->addAttribute( 'IsKanbanUsed', 'CHAR', "text(kanban4)", false, true, "text(kanban5)" );
     	
     	foreach( array('IsPlanningUsed', 'UseScrums', 'IsFixedRelease', 'ReleaseDuration') as $attribute )
     	{
     		$metadata->setAttributeVisible( $attribute, false );
     	}
    }
}
