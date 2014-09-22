<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

include "predicates/RequestCodeCommitPredicate.php";
include "persisters/BuildCodeRevisionPersister.php";

class BuildCodeMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Build' ) return;
        
		$metadata->addAttribute('BuildRevision', 'REF_SubversionRevisionId', 
				text('sourcecontrol35'), true, false, text('sourcecontrol36'), 15);
		
		$metadata->addPersister( new BuildCodeRevisionPersister() );
    }
}