<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ObjectMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	$policy = getFactory()->getAccessPolicy();
        foreach($metadata->getAttributesByGroup('permissions') as $attribute ) {
			if ( $policy->can_read_attribute($metadata->getObject(), $attribute, $metadata->getAttributeClass($attribute))) continue;
			if ( $metadata->IsReference($attribute) ) {
                $metadata->setAttributeVisible($attribute, false);
                $metadata->addAttributeGroup($attribute, 'system');
            }
            else {
                $metadata->removeAttribute($attribute);
            }
		}
    }
}