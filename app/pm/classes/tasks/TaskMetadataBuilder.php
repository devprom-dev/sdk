<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once "persisters/TaskFactPersister.php";
include_once "persisters/TaskTracePersister.php";
include_once "persisters/TaskDetailsPersister.php";
include_once "persisters/TaskAssigneePersister.php";
include_once "persisters/TaskTypePersister.php";
include_once "persisters/TaskUsedByPersister.php";
include "persisters/TaskTagsPersister.php";

class TaskMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;


		$metadata->addAttribute('Fact', 'FLOAT', translate('Затрачено'), true, true, '', 14 );
		$metadata->setAttributeType('Caption', 'VARCHAR');
		$metadata->setAttributeOrderNum('Priority', 11);
		$metadata->setAttributeOrderNum('Assignee', 12);
		$metadata->setAttributeOrderNum('Planned', 13);
		$metadata->setAttributeRequired('Planned', false);
		$metadata->setAttributeOrderNum('LeftWork', 14);
		$metadata->setAttributeVisible('LeftWork', false);
        $metadata->setAttributeRequired('OrderNum', true);
		$metadata->addPersister( new TaskFactPersister(array('Fact')) );

        $metadata->setAttributeRequired('Assignee', false);
		$metadata->addAttribute('TypeBase', 'REF_TaskTypeUnifiedId', translate('Тип'), false);
		$metadata->addAttributeGroup('TypeBase', 'system');
		$metadata->addPersister(new TaskTypePersister(array('TaskType')));

		$metadata->addAttribute('TraceTask', 'REF_TaskId', text(874), true, false, '', 80);
		$metadata->addAttribute('TraceInversedTask', 'REF_TaskId', text(1921), true, false, '', 81);
		$metadata->addPersister( new TaskTracePersister(array('TraceTask','TraceInversedTask')) );

        $metadata->addAttribute('ProjectPage', 'REF_ProjectPageId', translate('База знаний'), false);
        $metadata->addAttributeGroup('ProjectPage', 'trace');
        $metadata->addPersister( new TaskUsedByPersister() );

		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true, false, '', 75);
		$metadata->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), true);
		
		$metadata->setAttributeOrderNum( 'PlannedStartDate', 18 );
		$metadata->setAttributeOrderNum( 'PlannedFinishDate', 19 );
		$metadata->addPersister( new TaskAssigneePersister() );

        $metadata->addAttribute('DueWeeks', 'REF_DeadlineSwimlaneId', text(1898), false);
        $metadata->addPersister( new TaskDatesPersister() );

        foreach ( array('Assignee', 'Release', 'Caption', 'ChangeRequest', 'Priority', 'Planned', 'Fact', 'OrderNum', 'TaskType', 'TraceTask','PlannedFinishDate','Project') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'permissions');
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
        foreach ( array('Result','Author') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'system');
		}
		foreach ( array('Release','PlannedStartDate','PlannedFinishDate','StartDate','FinishDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'deadlines');
		}
		foreach ( array('TraceTask','TraceInversedTask','Watchers') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'trace');
		}
		foreach ( array('Caption','Description','Planned','Fact','LeftWork','Attachment','ChangeRequest') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'nonbulk');
		}

        $metadata->addPersister( new TaskDetailsPersister() );
		$this->removeAttributes( $metadata );
    }
    
    private function removeAttributes( $metadata )
    {
	    $metadata->removeAttribute( 'Controller' );
	    $metadata->removeAttribute( 'Comments' );
    }
}