<?php

include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceBaseIterator.php';

class QuestionTraceIterator extends RequestTraceBaseIterator
{
 	function getDisplayName()
 	{
 		$uid = new ObjectUID;
 		
 		$request_it = $this->getRef('ChangeRequest');
 		return $uid->getUidWithCaption( $request_it );
 	}
}
