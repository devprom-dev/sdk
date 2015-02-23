<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

include "predicates/TaskFactPersister.php";
include "persisters/TaskTracePersister.php";
include "persisters/TaskDetailsPersister.php";
include "persisters/TaskAssigneePersister.php";

class TaskMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

    	$object = $metadata->getObject();
        
    	$metadata->addPersister( new TaskDetailsPersister() );
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$metadata->addAttribute('Fact', 'FLOAT', 
			translate('Фактическая трудоемкость, ч.'), 
				is_object($methodology_it) && $methodology_it->IsTimeTracking(), 
					true, '', 8 );
		
		$metadata->setAttributeOrderNum('LeftWork', 9);

		if ( $methodology_it->IsTimeTracking() )
		{
			$metadata->addPersister( new TaskFactPersister );
		}
			
		$metadata->addAttribute('TraceTask', 'REF_TaskId', text(874), true);
		$metadata->addAttribute('TraceInversedTask', 'REF_TaskId', text(1921), true);
		$metadata->addPersister( new TaskTracePersister() );
		
		$metadata->setAttributeVisible('OrderNum', $methodology_it->get('IsRequestOrderUsed') == 'Y');
		$metadata->setAttributeVisible('Priority', $methodology_it->get('IsRequestOrderUsed') != 'Y');
		$metadata->setAttributeRequired('Assignee', !$methodology_it->IsParticipantsTakesTasks());

		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true, false, '', 110);
		$metadata->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), true);
		
		$metadata->setAttributeDescription( 'StartDate', text(1841) );
		$metadata->setAttributeDescription( 'FinishDate', text(1842) );

		$metadata->addPersister( new TaskAssigneePersister() );

    	foreach ( array('Assignee', 'Release', 'Caption', 'ChangeRequest', 'Priority', 'Planned', 'Fact', 'OrderNum', 'TaskType', 'TraceTask') as $attribute )
		{
			$metadata->addAttributeGroup($attribute, 'permissions');
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