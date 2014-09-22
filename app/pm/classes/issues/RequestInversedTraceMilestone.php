<?php

include_once "RequestTraceMilestone.php";

class RequestInversedTraceMilestone extends RequestTraceMilestone
{
 	function createIterator() 
 	{
 		return new RequestInversedTraceBaseIterator( $this );
 	}
}
