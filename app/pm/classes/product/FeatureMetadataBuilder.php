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

        $metadata->addAttribute('Children', 'REF_FeatureId', text(2437), false);

 		$metadata->addAttribute( 'Tags', 'REF_TagId', translate('Тэги'), false, false, '', 280 );
 		$tag = getFactory()->getObject('CustomTag');
 		$metadata->addPersister( new FeatureTagPersister() );
 		
		foreach ( array('Caption', 'Importance', 'Description', 'Tags', 'Type', 'StartDate', 'DeliveryDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
			$metadata->addAttributeGroup($attribute, 'permissions');
		}

		$metadata->setAttributeOrderNum('Workload', 132);

    	foreach ( array('Workload', 'Estimation', 'EstimationLeft') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('Type', 'ParentFeature', 'Children') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'hierarchy');
		}
    }
}