<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/watchers/persisters/WatchersPersister.php";
include_once "persisters/TaskDatesPersister.php";
include_once "persisters/TaskColorsPersister.php";
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
		    if ( $object->getAttributeType('Description') == '' ) {
                $request = $object->getAttributeObject('ChangeRequest');
                if ( getFactory()->getAccessPolicy()->can_read_attribute($request, 'Description') && $object instanceof Task ) {
                    $object->addAttribute('IssueDescription', 'WYSIWYG', text(2083), false, false, '', 40);
                }
            }
            $object->addAttribute('IssueFeature', 'REF_pm_FunctionId', translate('Функция'), false, false, '', 41);
			$object->addAttribute('IssueAttachment', 'REF_pm_AttachmentId', text(2123), false, false, '', 41);
			$object->addAttribute('IssueTraces', 'TEXT', text(1902), false, false, '', 42);
            $object->addAttributeGroup('IssueTraces', 'trace');
            $object->addAttribute('IssueVersion', 'VARCHAR', text(1334), false, false, '', 43);
            $object->addAttribute('IssueState', 'VARCHAR', text(2128), false, false, '', 43);
            $object->addAttributeGroup('IssueState', 'workflow');
			$object->addPersister( new TaskIssueArtefactsPersister() );
			foreach ( array('IssueDescription','IssueAttachment','IssueVersion', 'IssueTraces') as $attribute ) {
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
            $object->addAttributeGroup('Spent', 'hours');
            $object->addAttributeGroup('Spent', 'workload');
        }

		$object->addPersister( new TaskPhotoPersister() );

        if ( $methodology_it->HasReleases() ) {
            $object->addAttribute('PlannedRelease', 'REF_ReleaseId', translate('Релиз'), false);
            $object->addPersister( new TaskReleasePersister(array('PlannedRelease')) );
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

        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $object->addAttribute('UserGroup', 'REF_UserGroupId', text('user.group.name'), false);
        }

        $object->addAttribute('ProjectPage', 'REF_ProjectPageId', translate('База знаний'), false);
        $object->addAttributeGroup('ProjectPage', 'trace');
        $object->addPersister( new TaskUsedByPersister() );

        $object->addAttribute('PlanFact', 'FLOAT', '', false, false);
        $object->addPersister( new TaskPlanFactPersister() );
        $object->addAttributeGroup('PlanFact', 'system');
    }
}