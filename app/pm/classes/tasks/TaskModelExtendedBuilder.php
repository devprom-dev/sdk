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
include "persisters/TaskPlanFactPersister.php";
include_once "persisters/TaskUsedByPersister.php";

class TaskModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( $object->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

		$object->addPersister( new AttachmentsPersister(array('Attachment')) );
		$object->addPersister( new WatchersPersister(array('Watchers')) );

        if ( $object->getAttributeType('ChangeRequest') != '' ) {
            $object->addAttribute('IssueTraces', 'TEXT', text(1902), false, false, '', 42);
            $object->addAttributeGroup('IssueTraces', 'trace');
        }

		$comment = getFactory()->getObject('Comment');
		$object->addPersister( new CommentRecentPersister(array('RecentComment')) );

        if ( $methodology_it->IsTimeTracking() && $object->getAttributeType('Fact') != '' )
        {
            $object->addAttribute('Spent', 'REF_ActivityTaskId', translate('Списание времени'), false);
            $object->addPersister( new TaskSpentTimePersister(array('Spent')) );
            $object->addAttributeGroup('Spent', 'hours');
            $object->addAttributeGroup('Spent', 'workload');
        }

		$object->addPersister( new TaskPhotoPersister() );

        if ( $methodology_it->HasReleases() ) {
            $object->addAttribute('PlannedRelease', 'REF_ReleaseId', translate('Релиз'), false);
            $object->addPersister( new TaskReleasePersister(array('PlannedRelease')) );
        }

        $object->setAttributeVisible('Priority', true);
        $object->setAttributeRequired('Assignee', !$methodology_it->IsParticipantsTakesTasks());

        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $object->addAttribute('UserGroup', 'REF_UserGroupId', text('user.group.name'), false);
        }

        $object->addAttribute('ProjectPage', 'REF_ProjectPageId', translate('База знаний'), false);
        $object->addAttributeGroup('ProjectPage', 'trace');
        $object->addAttributeGroup('ProjectPage', 'non-form');
        $object->addPersister( new TaskUsedByPersister() );

        $object->addAttribute('PlanFact', 'FLOAT', text(2062), false, false);
        $object->setAttributeEditable('PlanFact', false);
        $object->addPersister( new TaskPlanFactPersister() );

        if ( !$methodology_it->HasPlanning() ) {
            $object->removeAttribute('Release');
        }
    }
}