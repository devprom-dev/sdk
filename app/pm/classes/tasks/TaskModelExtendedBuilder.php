<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include_once "persisters/TaskDatesPersister.php";
include "persisters/TaskSpentTimePersister.php";
include "persisters/TaskPhotoPersister.php";
include "persisters/TaskIssueArtefactsPersister.php";
include "persisters/TaskReleasePersister.php";
include "persisters/TaskColorsPersister.php";

class TaskModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( $object->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
		$object->addPersister( new AttachmentsPersister(array('Attachment')) );
		$object->addPersister( new WatchersPersister(array('Watchers')) );

		if ( $object->getAttributeType('ChangeRequest') != '' ) {
			$object->addAttribute('IssueDescription', 'WYSIWYG', text(2083), false, false, '', 40);
			$object->addAttribute('IssueAttachment', 'REF_pm_AttachmentId', text(2123), false, false, '', 41);
			$object->addAttribute('IssueTraces', 'TEXT', text(1902), false, false, '', 42);
            $object->addAttribute('IssueVersion', 'VARCHAR', text(1334), false, false, '', 43);
			$object->addPersister( new TaskIssueArtefactsPersister(array('IssueTraces','IssueDescription','IssueAttachment','IssueVersion')) );
			foreach ( array('IssueDescription','IssueAttachment','IssueVersion') as $attribute ) {
				$object->addAttributeGroup($attribute, 'source-issue');
			}
		}

		$object->addAttribute('RecentComment', 'WYSIWYG', translate('Комментарии'), false);
		$comment = getFactory()->getObject('Comment');
		$object->addPersister( new CommentRecentPersister(array('RecentComment')) );
		
        if ( $methodology_it->IsTimeTracking() && $object->getAttributeType('Fact') != '' )
        {
            $object->addAttribute('Spent', 'REF_ActivityTaskId', translate('Списание времени'), false);
            $object->addPersister( new TaskSpentTimePersister(array('Spent')) );
        }
 
		$object->addPersister( new TaskPhotoPersister() );

        if ( $methodology_it->HasReleases() ) {
            $object->addAttribute('PlannedRelease', 'REF_ReleaseId', translate('Релиз'), false);
            $object->addPersister( new TaskReleasePersister(array('PlannedRelease')) );
        }

        foreach ( array('StartDate','FinishDate','DueWeeks','PlannedStartDate','PlannedFinishDate','RecordCreated','RecordModified') as $attribute ) {
			$object->addAttributeGroup($attribute, 'dates');
		}

		foreach ( array('Planned','LeftWork','Fact','Spent') as $attribute ) {
			$object->addAttributeGroup($attribute, 'time');
		}
		$object->addPersister( new TaskColorsPersister() );

        $object->setAttributeVisible('Priority', true);
        $object->setAttributeRequired('Assignee', !$methodology_it->IsParticipantsTakesTasks());

        $typesCount = getFactory()->getObject('TaskType')->getRegistry()->Count(
            array( new FilterBaseVpdPredicate() )
        );
        if ( $typesCount < 1 ) {
            $object->setAttributeRequired('TaskType', false);
            $object->setAttributeVisible('TaskType', false);
            $object->setAttributeRequired('Caption', true);
        }

        $this->removeAttributes( $object, $methodology_it );
    }

    private function removeAttributes( $object, $methodology_it )
    {
        if ( !$methodology_it->HasPlanning() ) {
            $object->removeAttribute( 'Release' );
        }
        if ( !$methodology_it->TaskEstimationUsed() ) {
            $object->removeAttribute( 'Planned' );
            $object->removeAttribute( 'LeftWork' );
        }
        if ( !$methodology_it->IsTimeTracking() ) {
            $object->removeAttribute( 'Fact' );
        }
    }
}