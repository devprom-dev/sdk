<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";

include_once "persisters/RequestTagsPersister.php";
include_once "persisters/RequestFactPersister.php";
include_once "persisters/RequestTasksPersister.php";
include_once "persisters/RequestOwnerPersister.php";

class RequestMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_ChangeRequest' ) return;
        
    	$object = $metadata->getObject();
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		if ( $methodology_it->getId() > 0 && $methodology_it->IsTimeTracking() )
		{
			$metadata->addAttribute('Fact', 'FLOAT', translate('Затрачено, ч.'), false, false, '', 35);

		    $metadata->addPersister( new RequestFactPersister() );
			
		    $metadata->addAttributeGroup('Fact', 'transition');
		}
		
		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true);

		$metadata->addAttribute('Tags', 'REF_TagId', translate('Тэги'), true, false, '');
		
		$metadata->addPersister( new RequestTagsPersister() );
		
		$metadata->addAttribute( 'Links', 'REF_pm_ChangeRequestId', 'Связанные пожелания', true);
		
		$metadata->addAttributeGroup('Links', 'trace');
		
	    $metadata->addAttribute('Question', '', '', false);

		$metadata->addAttributeGroup('Question', 'trace');
	    
	    if ( $methodology_it->HasTasks() )
		{
			$metadata->addAttribute( 'Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, '', 200);
		    $metadata->addAttributeGroup('Tasks', 'transition');
		    $metadata->addAttributeGroup('Tasks', 'trace');
		    
			$metadata->addAttribute( 'OpenTasks', 'REF_pm_TaskId', translate('Незавершенные задачи'), false, false, '', 210);
		    $metadata->addAttributeGroup('OpenTasks', 'trace');
		}
		else
		{
	    	$metadata->setAttributeVisible( 'Owner', true );
		}
		
		$metadata->setAttributeVisible('Project', false);
		
		$metadata->removeAttribute( 'Environment' );
		
		if ( $methodology_it->HasVersions() )
		{
		    $metadata->setAttributeCaption('SubmittedVersion', text(1335));
		    
		    $metadata->setAttributeCaption('ClosedInVersion', text(1334));
		}
		
		$metadata->setAttributeType( 'Description', 'wysiwyg' );
		
		$strategy = $methodology_it->getEstimationStrategy();
		
		if ( $strategy->hasEstimationValue() )
		{
			$title = translate($metadata->getAttributeCaption('Estimation'));
			
			if ( strpos($title, ',') === false )
			{
				$metadata->setAttributeCaption( 
					'Estimation', $strategy->getDimensionText($title.',') 
				);
			}
				
			$title = translate($metadata->getAttributeCaption('EstimationLeft'));
			
			if ( strpos($title, ',') === false )
			{
				$metadata->setAttributeCaption( 
					'EstimationLeft', $strategy->getDimensionText($title.',') 
				);
			}
		}
		
		$permission_attributes = array(
				'Author', 
				'ClosedInVersion', 
				'Fact', 
				'Caption', 
				'Description', 
				'Owner', 
				'Priority', 
				'Function', 
				'OrderNum',
				'Type',
				'PlannedRelease',
				'Estimation');
		
    	foreach ( $permission_attributes as $attribute )
		{
		    $metadata->addAttributeGroup($attribute, 'permissions');
		}

        if ( $methodology_it->HasMilestones() )
		{	
			$metadata->addAttribute('Deadlines', 'REF_pm_MilestoneId', translate('Сроки'), true, false, '');

			$metadata->addPersister( new RequestMilestonesPersister() );
	    }
		
		$metadata->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), true);

		$metadata->setAttributeDescription('StartDate', text(1839));
		
		$metadata->setAttributeDescription('FinishDate', text(1840));
		
		$metadata->setAttributeVisible('OrderNum', $methodology_it->get('IsRequestOrderUsed') == 'Y');

		$metadata->addPersister( new RequestOwnerPersister() );
		
		$this->removeAttributes( $metadata, $methodology_it );
    }
    
    private function removeAttributes( & $metadata, & $methodology_it )
    {
    	if ( $methodology_it->getId() > 0 && !$methodology_it->RequestEstimationUsed() )
		{
		    $metadata->removeAttribute( 'Estimation' );
		}
		
    	if ( $methodology_it->getId() > 0 && !$methodology_it->HasFeatures() )
		{
		    $metadata->removeAttribute( 'Function' );
		}
		
 	 	if ( $methodology_it->getId() > 0 && !$methodology_it->HasVersions() )
 	 	{
		    $metadata->removeAttribute( 'SubmittedVersion' );

	 	 	$metadata->removeAttribute( 'ClosedInVersion' );
 	 	}
 	 	
	 	if ( $methodology_it->getId() > 0 && !$methodology_it->HasReleases() )
 	 	{
	 	 	$metadata->removeAttribute( 'PlannedRelease' );
 	 	}
 	 	
        $strategy = $methodology_it->getEstimationStrategy();
			
		if ( $methodology_it->getId() > 0 && !$strategy->hasEstimationValue() )
		{
		    $metadata->removeAttribute( 'Estimation' );
		    
		    $metadata->removeAttribute( 'EstimationLeft' );
		}
		
		if ( ! $strategy instanceof EstimationHoursStrategy )
		{
			$metadata->removeAttribute( 'EstimationLeft' );
		}
 		
   	 	if ( $methodology_it->getId() > 0 && $methodology_it->HasTasks() )
 	 	{
		    $metadata->removeAttribute( 'Owner' );
 	 	}
 	 	
 	 	if ( $methodology_it->get('IsRequestOrderUsed') != 'Y' )
 	 	{
 	 		$metadata->removeAttribute('OrderNum');
 	 	}
    }
}