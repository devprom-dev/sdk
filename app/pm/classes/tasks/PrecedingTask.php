<?php

include_once "Task.php";
include "PrecedingTaskIterator.php";

class PrecedingTask extends Task
{
 	function createIterator()
 	{
 		return new PrecedingTaskIterator( $this );
 	}
 	
 	function getStatableClassName()
 	{
 		return "task";
 	}
}
