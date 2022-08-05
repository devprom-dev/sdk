<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

/*
 *  ALTER TABLE pm_Task ADD NewField VARCHAR(255);
 */

class TaskMetadataBuilderExample2 extends ObjectMetadataEntityBuilder
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Task ) return; // extend the model for Task entity only

        $metadata->addAttribute('NewField', 'VARCHAR',
            text('example23'), true, true, text('example22'), 15);
    }
}
