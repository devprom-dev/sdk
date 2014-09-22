<?php

include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceMilestone.php';
include "MilestoneTraceRequestIterator.php";

class MilestoneTraceRequest extends RequestTraceMilestone
{
 	function createIterator()
 	{
 		return new MilestoneTraceRequestIterator( $this );	
 	}
}
