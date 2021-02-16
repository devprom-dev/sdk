<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tags/persisters/FeatureTagPersister.php";

include "persisters/FeatureTitlePersister.php";
include "persisters/FeatureHierarchyPersister.php";

class FeatureMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Function' ) return;

		$metadata->setAttributeType('Description', 'WYSIWYG');

		$metadata->setAttributeDescription('StartDate', text(1837));
	    $metadata->setAttributeDescription('DeliveryDate', text(1838));
    	
 		$metadata->addPersister( new FeatureTitlePersister() );
 		$metadata->addPersister( new FeatureHierarchyPersister() );

        $metadata->addAttribute('ChildrenFeatures', 'REF_FeatureId', text(2437), false);

 		$metadata->addAttribute( 'Tags', 'REF_TagId', translate('Тэги'), false, false, '', 280 );
 		$metadata->addPersister( new FeatureTagPersister() );
 		
		foreach ( array('Caption', 'Importance', 'Description', 'StartDate', 'DeliveryDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
        foreach ( array('Caption', 'Importance', 'Description', 'Tags', 'Type', 'StartDate', 'DeliveryDate') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }

        $metadata->addAttribute('Request', 'REF_pm_ChangeRequestId', text(808), true, false, '', 140);
		$metadata->setAttributeOrderNum('Workload', 132);

    	foreach ( array('Workload', 'Estimation', 'EstimationLeft') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('Type', 'ParentFeature', 'ChildrenFeatures') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'hierarchy');
		}

        $metadata->addAttributeGroup('ParentFeature', 'hierarchy-parent');
        $metadata->addAttributeGroup('Type', 'type');
        $metadata->addAttributeGroup('Type', 'skip-tooltip');
        $metadata->addAttributeGroup('ParentFeature', 'skip-tooltip');
    }
}