<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include "persisters/ComponentDetailsPersister.php";
include "persisters/ComponentRequestsPersister.php";

class ComponentMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Component ) return;

        $metadata->addAttributeGroup('ParentComponent', 'hierarchy-parent');
        $metadata->addPersister( new ObjectHierarchyPersister() );
        $metadata->addAttribute('Children', 'REF_ComponentId', text(2437), false);

        foreach ( array('Caption', 'Description', 'Type', 'ParentComponent') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'tooltip');
        }
        foreach ( array('ParentComponent', 'Children') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'hierarchy');
        }
        $metadata->addPersister(new ComponentDetailsPersister());

        $metadata->addAttribute('Requests', 'REF_RequestId',
            getSession()->IsRDD() ? text(1805) : text(806), true);
        $metadata->addAttribute('RequestsCount', 'INTEGER', text(3318), false);

        $metadata->addPersister(new ComponentRequestsPersister());
        $metadata->addAttributeGroup('Requests', 'trace');
        $metadata->addAttributeGroup('Type', 'customattribute-descriptor');

        $metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true, false, '', 75);
        $metadata->addPersister( new AttachmentsPersister() );
    }
}