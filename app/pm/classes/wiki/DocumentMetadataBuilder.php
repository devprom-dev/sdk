<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

include "persisters/DocumentStatePersister.php";

class DocumentMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'WikiPage' ) return;

    	$metadata->addPersister( new DocumentStatePersister() );
    }
}