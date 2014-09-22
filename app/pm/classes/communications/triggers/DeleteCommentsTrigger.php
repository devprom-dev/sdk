<?php

include_once SERVER_ROOT_PATH.'core/classes/model/events/SystemTriggersBase.php';

class DeleteCommentsTrigger extends SystemTriggersBase
{
	function process( $object_it, $kind, $content = array(), $visibility = 1) 
	{
		global $model_factory;
		
		if ( $kind != TRIGGER_ACTION_DELETE ) return;
	    
    	$comment = $model_factory->getObject('Comment');
		
	    $comment->setNotificationEnabled(false);
		
	    $comment_it = $comment->getAllForObject($object_it);
			
		for($i = 0; $i < $comment_it->count(); $i++) 
		{
			$comment->delete($comment_it->getId());
			
			$comment_it->moveNext();
		}
	}
}
 