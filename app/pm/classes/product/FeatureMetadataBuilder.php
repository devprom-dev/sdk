<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/tags/persisters/FeatureTagPersister.php";

class FeatureMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Function' ) return;

		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
 		$tag = getFactory()->getObject('CustomTag');
 		
 		$metadata->addAttribute( 'Tags', 'REF_TagId', translate('в§уш'), false, false, '', 40 );
 		
 		$metadata->addPersister( new FeatureTagPersister() );
 		
		foreach ( array('Caption', 'Importance', 'Description') as $attribute )
		{
			$metadata->addAttributeGroup($attribute, 'tooltip');

			$metadata->addAttributeGroup($attribute, 'permissions');
		}
    }
}