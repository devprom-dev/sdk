<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBase.php";
include_once "IssueStateIterator.php";

class IssueState extends StateBase
{
 	function createIterator()
 	{
 		return new IssueStateIterator( $this );
 	}
 	
 	function getObjectClass()
 	{
 		return 'request';
 	}
 	
 	function getDisplayName()
 	{
 		return text(903);
 	}
}
