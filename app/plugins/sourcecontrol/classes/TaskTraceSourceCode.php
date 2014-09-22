<?php

include_once SERVER_ROOT_PATH."pm/classes/tasks/TaskTraceBase.php";

class TaskTraceSourceCode extends TaskTraceBase
{
 	function getObjectClass()
 	{
 		return 'SubversionRevision';
 	}
}