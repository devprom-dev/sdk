<?php

include_once SERVER_ROOT_PATH.'pm/classes/notificators/EntityModifyProjectTrigger.php';

class IssueModifyProjectTrigger extends EntityModifyProjectTrigger
{
	protected function checkEntity( $object_it )
	{
		switch( $object_it->object->getEntityRefName() )
	    {
	        case 'pm_ChangeRequest':
	        	return true;
	    }
	    
	    return false;
	}
	
	protected function & getObjectReferences( & $object_it )
	{
 	    // prepare list of objects to be serilalized
 	    $references = array();
 	    $ids = $object_it->idsToArray();
        
 	    $type = getFactory()->getObject('pm_IssueType');
 	    $type->addFilter( new FilterInPredicate($object_it->fieldToArray('Type')) );
 	    
 	    $priority = getFactory()->getObject('Priority');
 	    $priority->addFilter( new FilterInPredicate($object_it->fieldToArray('Priority')) );
 	    
 	    $request = getFactory()->getObject('pm_ChangeRequest');
 	    $request->addFilter( new FilterInPredicate($ids) );
 	    
 	    $trace = getFactory()->getObject('pm_ChangeRequestTrace');
		$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $ids) ); 
 	    
 	    $link = getFactory()->getObject('pm_ChangeRequestLink');
 	    $link->addFilter( new RequestLinkedFilter($ids) ); 
 	    
		$attachment = getFactory()->getObject('pm_Attachment');
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );

		$watcher = getFactory()->getObject2('pm_Watcher', $object_it);
		
 	    $task = getFactory()->getObject('Task');
 	    $task->addFilter( new FilterAttributePredicate('ChangeRequest', $ids) );
 	    
 	    $activity = getFactory()->getObject('Activity');
 	    $activity->addFilter( new ActivityRequestPredicate($ids) );
 	    
		$comment = getFactory()->getObject('Comment');
		$comment->addFilter( new CommentObjectFilter($object_it) );
		
 	    $part_ids = array_unique(
		 	    		array_filter(
		 	    				array_merge(
					 	    		$request->getAll()->fieldToArray('Owner'),
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
 	    $references[] = $priority; 
 	    $references[] = $request;
		$references[] = $trace;
 	    $references[] = $link;
		$references[] = $attachment;
 	    $references[] = $task;
 	    $references[] = $activity;
		$references[] = $comment;
		$references[] = $watcher;
 	    
		return $references;
	}
}
 