<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

class ActivityMetadataBuilderExample extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Activity ) return; // extend the model for Activity entity only

    	/*
    	 * DB model has to be extended:
    	 * ALTER TABLE pm_Activity ADD COLUMN IsOvertype CHAR(1) DEFAULT 'N';
    	 */
		$metadata->addAttribute(
				'IsOvertime', 		// system name of the attribute, mapped onto table column 
				'CHAR', 			// db-type of the attribute 
				text('example22'),	// attribute title  
				true, 				// attribute is visible on forms and lists by default
				true,				// attributes is persisted (corresponding column should be in place in DB)
				''					// description of the attribute is displayed on form
		);
    }
}
