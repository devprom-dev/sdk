<?php

include_once SERVER_ROOT_PATH.'pm/classes/notificators/EntityModifyProjectTrigger.php';

class TaskModifyProjectTrigger extends EntityModifyProjectTrigger
{
	protected function checkEntity( $object_it )
	{
		return $object_it->object->getEntityRefName() == 'pm_Task';
	}
	
	protected function & getObjectReferences( & $object_it )
	{
 	    // prepare list of objects to be serilalized
 	    $references = array();
 	    $ids = $object_it->idsToArray();
        
 	    $type = getFactory()->getObject('pm_TaskType');
 	    $type->addFilter( new FilterInPredicate($object_it->fieldToArray('Type')) );
 	    
 	    $task = getFactory()->getObject('Task');
 	    $task->addFilter( new FilterInPredicate($ids) );
 	    
 	    $trace = getFactory()->getObject('pm_TaskTrace');
		$trace->addFilter( new FilterAttributePredicate('Task', $ids) ); 
 	    
		$attachment = getFactory()->getObject('pm_Attachment');
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );
		
 	    $activity = getFactory()->getObject('Activity');
 	    $activity->addFilter( new FilterAttributePredicate('Task',$ids) );
 	    
		$comment = getFactory()->getObject('Comment');
		$comment->addFilter( new CommentObjectFilter($object_it) );
		
 	    $references[] = $task;
		$references[] = $trace;
		$references[] = $attachment;
 	    $references[] = $activity;
		$references[] = $comment;
 	    
		return $references;
	}
}
 