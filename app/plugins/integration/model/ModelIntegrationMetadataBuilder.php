<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include 'persisters/ModelIntegrationPersister.php';

class ModelIntegrationMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !in_array($metadata->getObject()->getEntityRefName(), array('pm_ChangeRequest','pm_Task','pm_ReviewRequest')) ) return;

		$metadata->addAttribute('IntegrationLink', 'VARCHAR', text('integration8'), false, false);
        $metadata->addAttributeGroup('IntegrationLink', 'trace');
        $metadata->addAttributeGroup('IntegrationLink', 'non-form');
        $metadata->addAttribute('IntegrationRef', 'REF_IntegrationLinkId', text('integration23'), false, false);
        $metadata->addAttributeGroup('IntegrationRef', 'system');
        $metadata->addAttributeGroup('IntegrationRef', 'trace');
		$metadata->addPersister( new ModelIntegrationPersister(array('IntegrationLink')) );
    }
}
