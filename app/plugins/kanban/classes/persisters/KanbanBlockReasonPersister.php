<?php

class KanbanBlockReasonPersister extends ObjectSQLPersister
{
	function map( & $parms )
	{
	}

 	function modify( $object_id, $parms )
 	{
		if ( $parms['BlockReason'] == '' ) return;
		if ( $_REQUEST['TransitionComment'] == '' ) return;

		$comment = getFactory()->getObject('Comment');
		$comment->setNotificationEnabled(false);

		$comment->add_parms(
			array (
				'ObjectId' => $object_id,
				'ObjectClass' => get_class($this->getObject()),
				'AuthorId' => getSession()->getUserIt()->getId(),
				'Caption' => $_REQUEST['TransitionComment']
			)
		);
 	}
}