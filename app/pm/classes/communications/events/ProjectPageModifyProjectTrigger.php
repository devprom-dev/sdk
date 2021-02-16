<?php
include_once SERVER_ROOT_PATH.'pm/classes/notificators/EntityModifyProjectTrigger.php';

class ProjectPageModifyProjectTrigger extends EntityModifyProjectTrigger
{
	function checkEntity( $object_it ) {
	    return $object_it->object instanceof ProjectPage;
	}

    static function getObjectReferences( $object_it )
	{
 	    // prepare list of objects to be serilalized
 	    $references = array();
        
 	    $object = getFactory()->getObject('ProjectPage');
 	    $object->addFilter( new FilterInPredicate($object_it->idsToArray()) );
 	    $object->setSortDefault( new SortDocumentClause() );
 	    $references[] = $object;
 	    
 	    $attachment = getFactory()->getObject('WikiPageFile');
		$attachment->addFilter( new FilterAttributePredicate('WikiPage', $object_it->idsToArray()) );
		$references[] = $attachment;

 	    $changes = getFactory()->getObject('WikiPageChange');
		$changes->addFilter( new FilterAttributePredicate('WikiPage', $object_it->idsToArray()) );
		$references[] = $changes;
		
		$comment = getFactory()->getObject('Comment');
		$comment->addFilter( new CommentObjectFilter($object_it) );
		$references[] = $comment;

		return $references;
	}
}
 