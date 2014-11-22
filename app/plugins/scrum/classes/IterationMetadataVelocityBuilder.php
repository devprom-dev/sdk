<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class IterationMetadataVelocityBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Release' ) return;
    
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
        $strategy = $methodology_it->getEstimationStrategy();
        
 		$metadata->addAttribute( 'Velocity', 'FLOAT', 
 				preg_replace('/:|\%1/', '', $strategy->getVelocityText($metadata->getObject())), false );
    }
}