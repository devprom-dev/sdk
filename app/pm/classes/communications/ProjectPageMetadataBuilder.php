<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ProjectPageMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof ProjectPage ) return;

        foreach ( array('Estimation','Importance','PageType') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
            $metadata->setAttributeVisible($attribute, false);
        }

        foreach ( array('State') as $attribute ) {
            $metadata->removeAttribute($attribute);
        }
    }
}