<?php

include_once SERVER_ROOT_PATH.'pm/classes/issues/RequestTraceQuestion.php';
include "QuestionTraceIterator.php";

class QuestionTraceRequest extends RequestTraceQuestion
{
 	function createIterator()
 	{
 		return new QuestionTraceIterator( $this );	
 	}
}
