<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include 'persisters/ModelIntegrationPersister.php';

class ModelIntegrationMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !in_array($metadata->getObject()->getEntityRefName(), array('pm_ChangeRequest','pm_Task')) ) return;

		$metadata->addAttribute('IntegrationLink', 'VARCHAR', text('integration8'), true, false);
		$metadata->addAttributeGroup('IntegrationLink', 'additional');
		$metadata->addPersister( new ModelIntegrationPersister(array('IntegrationLink')) );
    }
}
