<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/CapacityPersister.php";
include_once "persisters/ReleaseMetricsPersister.php";

class ReleaseMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( ! $metadata->getObject() instanceof Release ) return;

        $metadata->setAttributeRequired('FinishDate', true);
        $metadata->addAttribute('EstimatedStartDate', 'DATETIME', translate('Оценка начала'), false, false);
        $metadata->addAttribute('EstimatedFinishDate', 'DATETIME', translate('Оценка окончания'), false, false);
        $metadata->addPersister( new ReleaseMetricsPersister() );

        $metadata->addAttributeGroup('Caption', 'alternative-key');

        $metadata->setAttributeCaption('Caption', translate('Название'));
    	$metadata->addAttribute( 'PlannedCapacity', 'FLOAT', text(1421), false );
        $metadata->addAttribute( 'LeftCapacityInWorkingDays', 'FLOAT', text(1422), false );
 	    $metadata->addPersister( new CapacityPersister() );
        $metadata->setAttributeType('StartDate', 'DATE');
        $metadata->setAttributeType('FinishDate', 'DATE');

        foreach ( array('StartDate','FinishDate','Caption','Description') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }
        foreach ( array('InitialEstimationError', 'InitialBugsInWorkload') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( !$methodology_it->IsAgile() ) {
            foreach ( array('InitialVelocity') as $attribute ) {
                $metadata->addAttributeGroup($attribute, 'system');
            }
            $metadata->setAttributeVisible('InitialVelocity', false);
            $metadata->setAttributeRequired('InitialVelocity', false);
        }
        else {
            $metadata->addAttributeGroup('InitialVelocity', 'nonbulk');
        }

        foreach ( array('IsClosed') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'bulk');
        }
    }
}