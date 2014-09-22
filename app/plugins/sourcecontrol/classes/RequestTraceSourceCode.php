<?php

include_once SERVER_ROOT_PATH."pm/classes/issues/RequestTraceBase.php";

class RequestTraceSourceCode extends RequestTraceBase
{
 	function getObjectClass()
 	{
 		return 'SubversionRevision';
 	}
}
