<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/TaskFactPersister.php";
include_once "persisters/TaskTracePersister.php";
include_once "persisters/TaskDetailsPersister.php";
include_once "persisters/TaskAssigneePersister.php";
include_once "persisters/TaskTypePersister.php";

class TaskMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;

    	$metadata->addPersister( new TaskDetailsPersister() );
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$metadata->addAttribute('Fact', 'FLOAT', 
			translate('Затрачено'), is_object($methodology_it) && $methodology_it->IsTimeTracking(), true, '', 14 );

		$metadata->setAttributeType('Caption', 'VARCHAR');
		$metadata->setAttributeOrderNum('Priority', 11);
		$metadata->setAttributeOrderNum('Assignee', 12);
		$metadata->setAttributeOrderNum('Planned', 13);
		$metadata->setAttributeRequired('Planned', false);
		$metadata->setAttributeOrderNum('LeftWork', 14);
		$metadata->setAttributeVisible('LeftWork', false);

		if ( $methodology_it->IsTimeTracking() ) {
			$metadata->addPersister( new TaskFactPersister(array('Fact')) );
		}

		$metadata->addAttribute('TypeBase', 'REF_TaskTypeUnifiedId', translate('Тип'), false);
		$metadata->addAttributeGroup('TypeBase', 'system');
		$metadata->addPersister(new TaskTypePersister(array('TaskType')));

		$metadata->addAttribute('TraceTask', 'REF_TaskId', text(874), true, false, '', 80);
		$metadata->addAttribute('TraceInversedTask', 'REF_TaskId', text(1921), true, false, '', 81);
		$metadata->addPersister( new TaskTracePersister(array('TraceTask','TraceInversedTask')) );
		
		$metadata->setAttributeVisible('OrderNum', $methodology_it->get('IsRequestOrderUsed') == 'Y');
		$metadata->setAttributeVisible('Priority', $methodology_it->get('IsRequestOrderUsed') != 'Y');
		$metadata->setAttributeRequired('Assignee', !$methodology_it->IsParticipantsTakesTasks());

		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true, false, '', 75);
		$metadata->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), true);
		
		$metadata->setAttributeOrderNum( 'PlannedStartDate', 18 );
		$metadata->setAttributeOrderNum( 'PlannedFinishDate', 19 );

		$metadata->addPersister( new TaskAssigneePersister() );

    	foreach ( array('Assignee', 'Release', 'Caption', 'ChangeRequest', 'Priority', 'Planned', 'Fact', 'OrderNum', 'TaskType', 'TraceTask') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'permissions');
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
        foreach ( array('Result') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('PlannedStartDate','PlannedFinishDate','StartDate','FinishDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'deadlines');
		}
		foreach ( array('TraceTask','TraceInversedTask','Watchers') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'trace');
		}
		foreach ( array('Caption','Description','Planned','Fact','LeftWork','Attachment','ChangeRequest') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'nonbulk');
		}

		$this->removeAttributes( $metadata, $methodology_it );
    }
    
    private function removeAttributes( & $metadata, & $methodology_it )
    {
	    $metadata->removeAttribute( 'Controller' );
	    
	    $metadata->removeAttribute( 'Comments' );

        if ( $methodology_it->getId() > 0 && !$methodology_it->HasPlanning() )
        {
            $metadata->removeAttribute( 'Release' );
        }
        
		if ( $methodology_it->getId() > 0 && !$methodology_it->TaskEstimationUsed() ) 
		{
		    $metadata->removeAttribute( 'Planned' );
		    $metadata->removeAttribute( 'LeftWork' );
		}

		if ( $methodology_it->getId() > 0 && !$methodology_it->IsTimeTracking() ) 
		{
		    $metadata->removeAttribute( 'Fact' );
		}
    }
}