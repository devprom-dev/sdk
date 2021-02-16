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

    static function getObjectReferences( $object_it )
	{
 	    // prepare list of objects to be serilalized
 	    $references = array();
 	    $ids = $object_it->idsToArray();
        
 	    $request = getFactory()->getObject(get_class($object_it->object));
		$persisters = $request->getPersisters();
		foreach( $persisters as $key => $persister ) {
			if ( $persister instanceof IssueAuthorPersister ) {
				unset($persisters[$key]);
			}
		}
		$request->setPersisters($persisters);
 	    $request->addFilter( new FilterInPredicate($ids) );
        $references[] = $request;

 	    $trace = getFactory()->getObject('pm_ChangeRequestTrace');
		$trace->addFilter( new FilterAttributePredicate('ChangeRequest', $ids) );
        $references[] = $trace;

 	    $link = getFactory()->getObject('pm_ChangeRequestLink');
 	    $link->addFilter( new RequestLinkedFilter($ids) );
        $references[] = $link;

		$attachment = getFactory()->getObject('pm_Attachment');
		$attachment->addFilter( new AttachmentObjectPredicate($object_it) );
        $references[] = $attachment;


 	    $task = getFactory()->getObject('Task');
 	    $task->addFilter( new FilterAttributePredicate('ChangeRequest', $ids) );
 	    $taskIt = $task->getAll();
        $references[] = $task;

 	    $activityRequest = getFactory()->getObject('Activity');
 	    $activityRequest->addFilter( new FilterAttributePredicate('Issue', $ids) );
        $references[] = $activityRequest;

 	    if ( $taskIt->count() > 0 ) {
 	        $references = array_merge($references,
                TaskModifyProjectTrigger::getObjectReferences($taskIt));
        }

		$comment = getFactory()->getObject('Comment');
		$comment->addFilter( new CommentObjectFilter($object_it) );
		$comment->addSort( new SortOrderedClause() );
        $references[] = $comment;

        $watcher = getFactory()->getObject2('pm_Watcher', $object_it);
        $references[] = $watcher;

        $tag = getFactory()->getObject('pm_RequestTag');
        $tag->addFilter( new FilterAttributePredicate('Request', $ids) );
        $references[] = $tag;

		return $references;
	}
}
 