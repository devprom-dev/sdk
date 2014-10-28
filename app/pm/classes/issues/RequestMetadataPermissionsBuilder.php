<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class RequestMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_ChangeRequest' ) return;

    	$policy = getFactory()->getAccessPolicy();

        foreach(array_keys($metadata->getAttributes()) as $attribute )
		{
			if ( !$policy->can_read_attribute($metadata->getObject(), $attribute, $metadata->getAttributeClass($attribute)) )
			{
				$metadata->removeAttribute($attribute);
			}
		}
    }
}