<?php

include_once SERVER_ROOT_PATH."cms/classes/ObjectMetadataEntityBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include_once "persisters/RequestTagsPersister.php";
include_once "persisters/RequestTasksPersister.php";
include_once "persisters/RequestDetailsPersister.php";
include_once "persisters/RequestOwnerPersister.php";
include_once "persisters/RequestMilestonesPersister.php";
include_once "persisters/RequestFeaturePersister.php";
include_once "persisters/RequestQuestionsPersister.php";
include_once "persisters/RequestColorsPersister.php";
include "persisters/RequestTypePersister.php";

class RequestMetadataBuilder extends ObjectMetadataEntityBuilder 
{
    public function build( ObjectMetadata $metadata )
    {
    	if ( !$metadata->getObject() instanceof Request) return;

		$metadata->addPersister( new WatchersPersister(array('Watchers')) );
		$metadata->addPersister( new RequestOwnerPersister() );
        $metadata->addPersister( new RequestFeaturePersister() );
        $metadata->addPersister( new RequestColorsPersister() );

        $metadata->setAttributeOrderNum('State', 25);
        $metadata->setAttributeOrderNum('Priority', 5);

    	$metadata->setAttributeType('Author', 'REF_IssueAuthorId');
		$metadata->setAttributeRequired('Author', false);
    	$metadata->addPersister( new RequestDetailsPersister() );
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->IsTimeTracking() )
		{
			$metadata->addAttributeGroup('Fact', 'nonbulk');
			$metadata->addAttributeGroup('FactTasks', 'system');
		}

		$metadata->addAttribute('Attachment', 'REF_pm_AttachmentId', translate('Приложения'), true);
		$metadata->addAttribute('Tags', 'REF_TagId', translate('Тэги'), true, false, '');
		$metadata->addPersister( new RequestTagsPersister(array('Tags')) );

		$metadata->addAttribute( 'Links', 'REF_pm_ChangeRequestId', text(764), true);
		$metadata->addAttributeGroup('Links', 'trace');
        $metadata->addPersister( new IssueLinkedIssuesPersister(array('Links')) );

	    $metadata->addAttribute('Question', 'REF_QuestionId', text(2037), true);
		$metadata->addAttributeGroup('Question', 'trace');
        $metadata->addPersister( new RequestQuestionsPersister() );

		$metadata->setAttributeVisible( 'PlannedRelease', true );
		$metadata->setAttributeOrderNum( 'PlannedRelease', 75 );

		$metadata->setAttributeVisible( 'Owner', true );
	    if ( $methodology_it->HasTasks() ) {
			$metadata->addAttribute( 'Tasks', 'REF_pm_TaskId', translate('Задачи'), true, false, text(2010), 200);
		}

		if ( $methodology_it->HasTasks() ) {
            $metadata->addAttribute( 'OpenTasks', 'REF_pm_TaskId', text(2117), false, false, '', 210);
            $metadata->addAttributeGroup('OpenTasks', 'skip-network');
            $metadata->addPersister( new RequestTasksPersister() );
            if ( !$methodology_it->HasTasks() ) {
                $metadata->addAttributeGroup('OpenTasks', 'system');
            }
        }

		if ( $methodology_it->HasPlanning() ) {
			$metadata->setAttributeVisible('Iteration', 'true');
			$metadata->addPersister(new RequestIterationsPersister());
			$metadata->addAttributeGroup('Iteration', 'bulk');
		}
		else {
            $metadata->addAttributeGroup('Iteration', 'system');
        }

        $metadata->addAttribute('DueWeeks', 'REF_DeadlineSwimlaneId', text(1938), false);
        $metadata->addPersister( new RequestDueDatesPersister(array('DueWeeks')) );

        $metadata->setAttributeVisible('Project', false);

	    $metadata->setAttributeCaption('SubmittedVersion', text(1335));
	    $metadata->setAttributeCaption('ClosedInVersion', text(1334));

		$metadata->setAttributeType( 'Description', 'wysiwyg' );
		$metadata->setAttributeType( 'Function', 'REF_FeatureId' );

		$strategy = $methodology_it->getEstimationStrategy();
		if ( $methodology_it->RequestEstimationUsed() && $strategy->hasEstimationValue() ) {
			$title = translate($metadata->getAttributeCaption('Estimation'));
			if ( strpos($title, ',') === false ) {
				$metadata->setAttributeCaption( 
					'Estimation', $strategy->getDimensionText($title.',') 
				);
			}
		}

        if ( $methodology_it->HasMilestones() ) {
            $metadata->addAttribute('Deadlines', 'REF_pm_MilestoneId', text(2264), true, false, '', 180);
            $metadata->addPersister( new RequestMilestonesPersister() );
            $metadata->addAttributeGroup('Deadlines', 'deadlines');
            $metadata->addAttributeGroup('Deadlines', 'form-column-first');
        }

        $metadata->addAttribute('Watchers', 'REF_WatcherId', translate('Наблюдатели'), true);
        $metadata->addAttributeGroup('Watchers', 'additional');

        $metadata->setAttributeCaption('DeliveryDate', text(2289));
		$metadata->setAttributeDescription('DeliveryDate', text(2113));
		$metadata->setAttributeDescription('StartDate', text(1839));
		$metadata->setAttributeDescription('FinishDate', text(1840));
		foreach ( array('StartDate','FinishDate', 'PlannedRelease', 'Iteration', 'DeliveryDate', 'DueWeeks') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'deadlines');
		}
		$index = 190;
		$metadata->setAttributeOrderNum('StartDate', $index);
		$metadata->setAttributeOrderNum('FinishDate', $index+5);
		$metadata->setAttributeOrderNum('DeliveryDate', $index+10);

		$metadata->addAttribute('TypeBase', 'REF_RequestTypeUnifiedId', translate('Тип'), false);
		$metadata->addAttributeGroup('TypeBase', 'system');
        $metadata->addAttributeGroup('Type', 'type');
		$metadata->addPersister(new RequestTypePersister(array('Type')));

		$index = 210;
		$metadata->setAttributeOrderNum('Environment', $index);
		$metadata->setAttributeOrderNum('SubmittedVersion', $index+5);
		$metadata->setAttributeOrderNum('ClosedInVersion', $index+10);
		$metadata->setAttributeOrderNum('Author', $index+20);

        $metadata->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);

		foreach( array('Type','Function','ClosedInVersion','Author','Fact','OrderNum') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'additional');
		}
		foreach ( array('Caption','Description','Priority','Tags','Type','Project','ClosedInVersion','Owner','Links','Attachments','Author','Function','Tasks') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'tooltip');
		}
        foreach ( array('DueWeeks', 'Type') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-tooltip');
        }
        foreach ( array('OpenTasks', 'DueWeeks') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'non-form');
        }
        foreach ( array('DeliveryDate','EstimationLeft','Question') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'form-column-skipped');
        }
        foreach ( array('Tags','Watchers','Tasks', 'OpenTasks') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'skip-chart');
        }
		foreach ( array('Environment','Caption','Description','Estimation','Attachment') as $attribute ) {
			$metadata->addAttributeGroup($attribute, 'nonbulk');
		}

        $dates_attributes = array( 'EstimationLeft', 'Fact' );
        foreach ( $dates_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'workload');
            $metadata->addAttributeGroup($attribute, 'hours');
        }
        $metadata->addAttributeGroup('Estimation', 'workload');

        foreach( array('DeliveryDateMethod', 'EstimationLeft', 'SupportChannelEmail', 'EmailMessageId') as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'system');
        }

        if ( $strategy instanceof EstimationHoursStrategy ) {
            $metadata->resetAttributeGroup('EstimationLeft', 'system');
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
            'Estimation',
            'EstimationLeft',
            'Project',
            'Watchers',
            'Tags',
            'Links',
            'DeliveryDate',
            'RecentComment'
        );
        foreach ( $permission_attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'permissions');
        }

        $firstColumnAttributes = array(
            'Type',
            'Priority',
            'Estimation',
            'OrderNum',
            'PlannedRelease',
            'SubmittedVersion',
            'Iteration',
            'Owner'
        );
        foreach ( $firstColumnAttributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'form-column-first');
        }

        $attributes = array(
            'FinishDate',
            'State',
            'Project'
        );
        foreach ( $attributes as $attribute ) {
            $metadata->addAttributeGroup($attribute, 'form-column-skipped');
        }

        $priority = new Priority();
        $priorityIt = $priority->getByRef('IsDefault', 'Y');
        if ( $priorityIt->getId() != '' ) {
            $metadata->setAttributeDefault('Priority', $priorityIt->getId());
        }
    }
}