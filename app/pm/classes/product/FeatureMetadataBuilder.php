<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."core/classes/model/persisters/ObjectHierarchyPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/tags/persisters/FeatureTagPersister.php";
include "persisters/FeatureTitlePersister.php";
include "persisters/FeatureHierarchyPersister.php";

class FeatureMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Function' ) return;

		$metadata->setAttributeDescription('StartDate', text(1837));
        $metadata->setAttributeEditable('StartDate', false);
	    $metadata->setAttributeDescription('DeliveryDate', text(1838));
        $metadata->setAttributeEditable('DeliveryDate', false);

        $metadata->addAttributeGroup('ParentFeature', 'hierarchy-parent');
        $metadata->addPersister( new ObjectHierarchyPersister() );
        $metadata->addAttribute('Children', 'REF_FeatureId', text(2437), false);

 		$metadata->addPersister( new FeatureTitlePersister() );
 		$metadata->addPersister( new FeatureHierarchyPersister() );

 		$metadata->addAttribute( 'Tags', 'REF_TagId', translate('Тэги'), false, false, '', 280 );
 		$metadata->addPersister( new FeatureTagPersister() );

        $metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true, false, '', 75);

		foreach ( array('Caption', 'Importance', 'Description', 'StartDate', 'DeliveryDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
        foreach ( array('Attachment') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'non-bulk');
        }

        $metadata->addAttribute('Request', 'REF_pm_ChangeRequestId', text(808), true, false, '', 140);
		$metadata->setAttributeOrderNum('Workload', 132);

    	foreach ( array('Workload', 'Estimation', 'EstimationLeft') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('Type', 'ParentFeature', 'Children') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'hierarchy');
		}

        $metadata->addAttributeGroup('Type', 'type');
        $metadata->addAttributeGroup('Type', 'skip-tooltip');
        $metadata->addAttributeGroup('ParentFeature', 'skip-tooltip');

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        if ( $methodology_it->IsTimeTracking() ) {
            $metadata->addAttribute('Fact', 'FLOAT', translate('Затрачено'), false, false, '', 137);
            $metadata->addAttributeGroup('Fact', 'hours');
        }

        foreach ( array_keys($metadata->getAttributes()) as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }
    }
}