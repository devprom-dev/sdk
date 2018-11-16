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

    function process( $object_it, $kind, $content = array(), $visibility = 1)
    {
        if ( $kind != TRIGGER_ACTION_MODIFY ) return;
        if ( !$this->checkEntity($object_it) ) return;

        $references = $this->getObjectReferences($object_it);
        if ( !is_array($references) ) return;

        if ( getSession()->getProjectIt()->IsPortfolio() ) {
            if ( array_key_exists('PlannedRelease', $content) )
            {
                $release_it = getFactory()->getObject('Release')->getExact($content['PlannedRelease']);
                if ( $release_it->get('VPD') != '' && $release_it->get('VPD') != $object_it->get('VPD') ) {
                    return $this->moveEntity( $object_it, $release_it->getRef('Project'), $references );
                }
            }
            if ( array_key_exists('Iteration', $content) )
            {
                $release_it = getFactory()->getObject('Iteration')->getExact($content['Iteration']);
                if ( $release_it->get('VPD') != '' && $release_it->get('VPD') != $object_it->get('VPD') ) {
                    return $this->moveEntity( $object_it, $release_it->getRef('Project'), $references );
                }
            }
        }

        parent::process($object_it, $kind, $content, $visibility);
    }

    protected function & getObjectReferences( & $object_it )
	{
 	    // prepare list of objects to be serilalized
 	    $references = array();
 	    $ids = $object_it->idsToArray();
        
 	    $request = getFactory()->getObject('pm_ChangeRequest');
		$persisters = $request->getPersisters();
		foreach( $persisters as $key => $persister ) {
			if ( $persister instanceof IssueAuthorPersister ) {
				unset($persisters[$key]);
			}
		}
		$request->setPersisters($persisters);
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
		$comment->addSort( new SortOrderedClause() );
		
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
 