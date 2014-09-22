<?php

class MilestoneTraceRequestIterator extends RequestTraceBaseIterator
{
 	function getDisplayName()
 	{
 		$uid = new ObjectUID;
 		
 		$request_it = $this->getRef('ChangeRequest');
 		
 		return $uid->getUidWithCaption( $request_it );
 	}
}
