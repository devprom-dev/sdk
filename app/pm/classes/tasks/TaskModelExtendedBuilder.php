<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include "persisters/TaskSpentTimePersister.php";
include "persisters/TaskPhotoPersister.php";
include "persisters/TaskDatesPersister.php";
include "persisters/TaskIssueArtefactsPersister.php";
include "persisters/TaskReleasePersister.php";

class TaskModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( $object->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
		$object->addPersister( new AttachmentsPersister() );
		$object->addPersister( new WatchersPersister() );

		$object->addAttribute('IssueTraces', 'TEXT', text(1902), false);
		$object->addPersister( new TaskIssueArtefactsPersister() );
		
		$object->addAttribute('RecentComment', 'RICHTEXT', translate('Комментарии'), false);
		$comment = getFactory()->getObject('Comment');
		$object->addPersister( new CommentRecentPersister() );
		
        if ( $methodology_it->IsTimeTracking() )
        {
            $object->addAttribute('Spent', 'REF_ActivityTaskId', translate('Списание времени'), false);
            $object->addPersister( new TaskSpentTimePersister() );
        }
 
		$object->addPersister( new TaskPhotoPersister() );
		
		$object->addAttribute('DueDays', 'INTEGER', text(1890), false);
		$object->addAttribute('DueWeeks', 'REF_DeadlineSwimlaneId', text(1898), false);
		$object->addPersister( new TaskDatesPersister() );
		
		$object->addAttribute('PlannedRelease', 'REF_ReleaseId', translate('Релиз'), false);
		$object->addPersister( new TaskReleasePersister() );
    }
}