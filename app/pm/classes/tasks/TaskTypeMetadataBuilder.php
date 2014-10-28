<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

include "persisters/TaskTypeStagePersister.php";

class TaskTypeMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof TaskType ) return;

		$metadata->addAttribute('Stages', 'REF_pm_ProjectStageId', translate('Стадии процесса'), false);
		$metadata->addPersister( new TaskTypeStagePersister() );
		
		$metadata->setAttributeCaption('ReferenceName', text(1868));
		$metadata->setAttributeOrderNum('ReferenceName', 14);
		
		$metadata->setAttributeType('ParentTaskType', 'REF_TaskTypeBaseId');
		$metadata->setAttributeRequired('ParentTaskType', true);
		
		$metadata->setAttributeRequired('ReferenceName', true);
		
		$metadata->setAttributeDescription('ProjectRole', text(1849));
 		$metadata->setAttributeDescription( 'RelatedColor', text(1856) );
    }
}