<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include "persisters/TaskSpentTimePersister.php";

class TaskModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( $object->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
		$object->addPersister( new AttachmentsPersister() );
		
		$object->addPersister( new WatchersPersister() );
		
		$object->addAttribute('RecentComment', 'RICHTEXT', text(1198), false);
		
		$comment = getFactory()->getObject('Comment');
		
		$object->addPersister( new CommentRecentPersister() );
		
        if ( $methodology_it->IsTimeTracking() )
        {
            $object->addAttribute('Spent', 'REF_ActivityTaskId', translate('Списание времени'), false);
            
            $object->addPersister( new TaskSpentTimePersister() );
        }

		$object->addAttribute('AssigneeUser', 'REF_UserId', translate('Исполнитель'), false);
	}
}