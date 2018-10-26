<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/FieldWysiwygPersister.php";

class WysiwygMetadataBuilder extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
        $metadata->addPersister(new FieldWysiwygPersister());
	}
}
