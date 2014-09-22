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
		global $model_factory;
		
 	    // prepare list of objects to be serilalized
 	    $references = array();
        
 	    $type = getFactory()->getObject('pm_IssueType');
 	    
 	    $type->addFilter( new FilterInPredicate($object_it->fieldToArray('Type')) );
 	    
 	    $references[] = $type;
 	    
 	    $priority = getFactory()->getObject('Priority');
 	    
 	    $priority->addFilter( new FilterInPredicate($object_it->fieldToArray('Priority')) );
 	    
 	    $references[] = $priority; 
 	    
 	    $request = $model_factory->getObject('pm_ChangeRequest');
 	    
 	    $request->addFilter( new FilterInPredicate($object_it->idsToArray()) );
 	    
 	    $references[] = $request;
 	    
 	    $trace = $model_factory->getObject('pm_ChangeRequestTrace');
		
		$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $object_it->getId()) ); 
 	    
		$references[] = $trace;
		
 	    $link = $model_factory->getObject('pm_ChangeRequestLink');

 	    $link->addFilter( new RequestLinkedFilter($object_it->getId()) ); 
 	    
		$references[] = $link;
 	    
		$attachment = $model_factory->getObject('pm_Attachment');
				
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );
		
		$references[] = $attachment;

 	    $task = $model_factory->getObject('Task');
 	    
 	    $task->addFilter( new FilterAttributePredicate('ChangeRequest', $object_it->idsToArray()) );
 	    
 	    $references[] = $task;
 	    
 	    $activity = $model_factory->getObject('Activity');
 	    
 	    $activity->addFilter( new ActivityRequestPredicate($object_it->idsToArray()) );
 	    
 	    $references[] = $activity;
 	    
		$comment = $model_factory->getObject('Comment');
				
		$comment->addFilter( new CommentObjectFilter($object_it) );
		
		$references[] = $comment;

		return $references;
	}
}
 