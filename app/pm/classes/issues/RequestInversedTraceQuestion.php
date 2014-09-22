<?php

include_once "RequestTraceQuestion.php";

class RequestInversedTraceQuestion extends RequestTraceQuestion
{
 	function createIterator() 
 	{
 		return new RequestInversedTraceBaseIterator( $this );
 	}
}
