<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/IterationMetricsPersister.php";
include_once "persisters/CapacityPersister.php";
include_once "persisters/IterationTitlePersister.php";

class IterationMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Release' ) return;

    	$metadata->addPersister( new CapacityPersister() );
    	
    	$metadata->addAttribute('EstimatedStartDate', 'DATETIME', translate('Оценка начала'), false, false);
		$metadata->addAttribute('EstimatedFinishDate', 'DATETIME', translate('Оценка окончания'), false, false);
		$metadata->addPersister( new IterationMetricsPersister() );
 		
    	$metadata->addAttribute( 'Caption', 'TEXT', translate('Итерация'), false );
    	$metadata->addPersister( new IterationTitlePersister() );
    	
    	$metadata->setAttributeRequired('FinishDate', !getSession()->getProjectIt()->getMethodologyIt()->HasFixedRelease()); 
    	$metadata->setAttributeRequired('InitialVelocity', false);
    }
}