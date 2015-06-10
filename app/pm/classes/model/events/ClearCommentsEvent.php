<?php

class ClearCommentsEvent extends ObjectFactoryNotificator
{
 	function add( $object_it ) 
	{
	}

 	function modify( $prev_object_it, $object_it ) 
	{
	}

 	function delete( $object_it ) 
	{
		$comment_it = getFactory()->getObject('Comment')->getAllForObject($object_it);
		while( !$comment_it->end() ) 
		{
			$comment_it->object->delete($comment_it->getId());
			$comment_it->moveNext();
		}
		
		$log = new ChangeLog(new ObjectRegistrySQL());
		$log_it = $log->getRegistry()->Query(
				array ( 
						new ChangeLogItemFilter($object_it),
						new ChangeLogActionFilter('commented')
				)
		);
		while( !$log_it->end() ) 
		{
			$log->delete($log_it->getId());
			$log_it->moveNext();
		}		
	}
}