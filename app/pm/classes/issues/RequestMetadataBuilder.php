<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include_once "persisters/RequestTagsPersister.php";
include_once "persisters/RequestTasksPersister.php";
include_once "persisters/RequestDetailsPersister.php";
include_once "persisters/RequestOwnerPersister.php";
include "persisters/RequestTypePersister.php";

class RequestMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( $metadata->getObject()->getEntityRefName() != 'pm_ChangeRequest' ) return;

		$metadata->addPersister( new WatchersPersister(array('Watchers')) );
		$metadata->addPersister( new RequestOwnerPersister() );

		$metadata->addAttributeGroup('Customer', 'system');
    	$metadata->setAttributeType('Author', 'REF_IssueAuthorId');
		$metadata->setAttributeRequired('Author', false);
    	$metadata->addPersister( new RequestDetailsPersister(array('Author')) );
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->IsTimeTracking() )
		{
			$metadata->addAttributeGroup('Fact', 'transition');
			$metadata->addAttributeGroup('Fact', 'nonbulk');
			$metadata->addAttributeGroup('FactTasks', 'system');
		}

		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true);
		$metadata->addAttribute('Tags', 'REF_TagId', translate('Тэги'), true, false, '');
		$metadata->addPersister( new RequestTagsPersister(array('Tags')) );

		if ( $methodology_it->HasMilestones() )
		{
			$metadata->addAttribute('Deadlines', 'REF_pm_MilestoneId', translate('Сроки'), true, false, '', 180);
			$metadata->addPersister( new RequestMilestonesPersister(array('Deadlines')) );
			$metadata->addAttributeGroup('Deadlines', 'deadlines');
		}

		$metadata->addAttribute( 'Links', 'REF_pm_ChangeRequestId', text(764), true);
		$metadata->addAttributeGroup('Links', 'trace');
        $metadata->addPersister( new IssueLinkedIssuesPersister(array('Links')) );

	    $metadata->addAttribute('Question', 'REF_QuestionId', text(2037), false);
		$metadata->addAttributeGroup('Question', 'trace');

		$metadata->setAttributeVisible( 'PlannedRelease', true );
		$metadata->setAttributeOrderNum( 'PlannedRelease', 75 );

		$metadata->setAttributeVisible( 'Owner', true );

	    if ( $methodology_it->HasTasks() ) {
			$metadata->addAttribute( 'Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, text(2010), 200);
		    $metadata->addAttributeGroup('Tasks', 'transition');
			$metadata->addAttributeGroup('Owner', 'additional');
		}

		$metadata->addAttribute( 'OpenTasks', 'REF_pm_TaskId', text(2117), false, false, '', 210);
		$metadata->addAttributeGroup('OpenTasks', 'skip-network');
		$metadata->addPersister( new RequestTasksPersister(array('OpenTasks')) );
		if ( !$methodology_it->HasTasks() ) {
			$metadata->addAttributeGroup('OpenTasks', 'system');
		}

		if ( $methodology_it->HasPlanning() ) {
			$metadata->setAttributeVisible('Iteration', 'true');
			$metadata->addPersister(new RequestIterationsPersister());
			$metadata->addAttributeGroup('Iteration', 'bulk');
		}

		$metadata->setAttributeVisible('Project', false);
		$metadata->setAttributeVisible('Environment', true);

	    $metadata->setAttributeCaption('SubmittedVersion', text(1335));
	    $metadata->setAttributeCaption('ClosedInVersion', text(1334));
	    $metadata->setAttributeVisible('ClosedInVersion', true);
		foreach( array('ClosedInVersion','SubmittedVersion') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'bulk');
		}

		$metadata->setAttributeType( 'Description', 'wysiwyg' );
		$metadata->setAttributeType( 'Function', 'REF_FeatureId' );

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
			'Iteration',
			'Estimation'
		);
    	foreach ( $permission_attributes as $attribute )
		{
		    $metadata->addAttributeGroup($attribute, 'permissions');
		}

		$metadata->addAttribute('Watchers', 'REF_cms_UserId', translate('Наблюдатели'), true);
		$metadata->addAttributeGroup('Watchers', 'additional');
		$metadata->setAttributeVisible('OrderNum', $methodology_it->get('IsRequestOrderUsed') == 'Y');

		$metadata->addAttributeGroup('DeliveryDate', 'non-form');
		$metadata->setAttributeDescription('DeliveryDate', text(2113));
		$metadata->setAttributeDescription('StartDate', text(1839));
		$metadata->setAttributeDescription('FinishDate', text(1840));
		foreach ( array('StartDate','FinishDate', 'PlannedRelease', 'Iteration', 'DeliveryDate') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'deadlines');
		}
		$index = 190;
		$metadata->setAttributeOrderNum('StartDate', $index);
		$metadata->setAttributeOrderNum('FinishDate', $index+5);
		$metadata->setAttributeOrderNum('DeliveryDate', $index+10);

		$metadata->addAttribute('TypeBase', 'REF_RequestTypeUnifiedId', translate('Тип'), false);
		$metadata->addAttributeGroup('TypeBase', 'system');
		$metadata->addPersister(new RequestTypePersister(array('Type')));

		$index = 210;
		$metadata->setAttributeOrderNum('Environment', $index);
		$metadata->setAttributeOrderNum('SubmittedVersion', $index+5);
		$metadata->setAttributeOrderNum('ClosedInVersion', $index+10);
		$metadata->setAttributeOrderNum('Author', $index+20);
		$this->removeAttributes( $metadata, $methodology_it );

		foreach( array('Function','ClosedInVersion','Author','Fact') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'additional');
		}
		foreach ( array('Caption','Description','Priority','Tags','Type','Project','ClosedInVersion','Owner','Links','Attachments','Author') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
		foreach ( array('Caption','Description','Estimation','EstimationLeft','Attachment') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'nonbulk');
		}
    }
    
    private function removeAttributes( & $metadata, & $methodology_it )
    {
    	if ( $methodology_it->getId() > 0 && !$methodology_it->RequestEstimationUsed() ) {
		    $metadata->removeAttribute( 'Estimation' );
		}
		
    	if ( $methodology_it->getId() > 0 && !$methodology_it->HasFeatures() ) {
            $metadata->addAttributeGroup('Function', 'system');
		}
		
	 	if ( $methodology_it->getId() > 0 && !$methodology_it->HasReleases() ) {
	 	 	$metadata->removeAttribute( 'PlannedRelease' );
 	 	}
 	 	
        $strategy = $methodology_it->getEstimationStrategy();

		if ( $methodology_it->getId() > 0 && !$strategy->hasEstimationValue() ) {
		    $metadata->removeAttribute( 'Estimation' );
		    $metadata->removeAttribute( 'EstimationLeft' );
		}
		
		if ( ! $strategy instanceof EstimationHoursStrategy ) {
			$metadata->removeAttribute( 'EstimationLeft' );
		}
 		
 	 	if ( $methodology_it->get('IsRequestOrderUsed') != 'Y' ) {
 	 		$metadata->removeAttribute('OrderNum');
 	 	}

		if ( !$methodology_it->IsTimeTracking() )
		{
			$metadata->removeAttribute('Fact');
			$metadata->removeAttribute('FactTasks');
		}
	}
}