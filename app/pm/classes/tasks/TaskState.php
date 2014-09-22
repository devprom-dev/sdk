<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/StateBase.php";

class TaskState extends StateBase
{
 	function getObjectClass()
 	{
 		return 'task';
 	}
 	
 	function getDisplayName()
 	{
 		return text(1108);
 	}
}
