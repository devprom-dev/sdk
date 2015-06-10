<?php

include_once SERVER_ROOT_PATH."core/views/BulkFormBase.php";

class BulkFormAdmin extends BulkFormBase
{
 	function getCommandClass()
 	{
		return 'bulkcompleteadmin';
 	}
}