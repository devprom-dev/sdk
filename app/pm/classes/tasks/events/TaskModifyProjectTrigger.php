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
		
 	    $part_ids = array_unique(
		 	    		array_filter(
		 	    				array_merge(
					 	    		$task->getAll()->fieldToArray('Assignee'),
					 	    		$activity->getAll()->fieldToArray('Participant')
		 	    				),
								function($value) { return $value > 0; }	
		 	    		)
	 	    		);

 	    if ( count($part_ids) > 0 )
 	    {
	 	    $part = getFactory()->getObject('Participant');
	 	    $part->addFilter( new FilterInPredicate($part_ids) );
	 	    $references[] = $part;
	 	     
	 	    $part_role = getFactory()->getObject('ParticipantRole');
	 	    $part_role->addFilter( new FilterAttributePredicate('Participant', $part_ids) );
	 	    $references[] = $part_role;
	 	    
	 	    $project_role = getFactory()->getObject('ProjectRole');
	 	    $project_role->addFilter( new FilterInPredicate($part_role->getAll()->fieldToArray('ProjectRole')) );
	 	    $references[] = $project_role;
 	    } 

 	    $references[] = $type;
 	    $references[] = $task;
		$references[] = $trace;
		$references[] = $attachment;
 	    $references[] = $activity;
		$references[] = $comment;
 	    
		return $references;
	}
}
 