<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class TaskMetadataPermissionsBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;
    
		$policy = getFactory()->getAccessPolicy();
    			
        foreach( $metadata->getAttributesByGroup('permissions') as $attribute )
		{
			if ( !$policy->can_read_attribute($metadata->getObject(), $attribute) )
			{
				$metadata->removeAttribute($attribute);
			}
		}
    }
}