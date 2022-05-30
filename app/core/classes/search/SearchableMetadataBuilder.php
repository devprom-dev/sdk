<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include "persisters/ObjectSearchablePersister.php";

class SearchableMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
        $wysiwygAttributes = $metadata->getAttributesByType('wysiwyg');
    	if ( count($wysiwygAttributes) < 1 ) return;

		$metadata->addPersister( new ObjectSearchablePersister($wysiwygAttributes) );
    }
}