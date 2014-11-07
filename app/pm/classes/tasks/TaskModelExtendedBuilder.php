<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/attachments/persisters/AttachmentsPersister.php";
include_once SERVER_ROOT_PATH."pm/classes/comments/persisters/CommentRecentPersister.php";
include "persisters/TaskSpentTimePersister.php";
include "persisters/TaskPhotoPersister.php";

class TaskModelExtendedBuilder extends ObjectModelBuilder 
{
    public function build( Metaobject $object )
    {
        if ( $object->getEntityRefName() != 'pm_Task' ) return;

        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
        
		$object->addPersister( new AttachmentsPersister() );
		
		$object->addPersister( new WatchersPersister() );
		
		$object->addAttribute('RecentComment', 'RICHTEXT', translate('�����������'), false);
		
		$comment = getFactory()->getObject('Comment');
		
		$object->addPersister( new CommentRecentPersister() );
		
        if ( $methodology_it->IsTimeTracking() )
        {
            $object->addAttribute('Spent', 'REF_ActivityTaskId', translate('�������� �������'), false);
            
            $object->addPersister( new TaskSpentTimePersister() );
        }

		$object->addAttribute('AssigneeUser', 'REF_UserId', translate('�����������'), false);
		
		$object->addPersister( new TaskPhotoPersister() );
	}
}