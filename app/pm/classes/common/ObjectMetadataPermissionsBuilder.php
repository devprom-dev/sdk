<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ObjectMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	$policy = getFactory()->getAccessPolicy();
        foreach($metadata->getAttributesByGroup('permissions') as $attribute ) {
			if ( $policy->can_read_attribute($metadata->getObject(), $attribute, $metadata->getAttributeClass($attribute))) continue;
			$metadata->removeAttribute($attribute);
		}
    }
}