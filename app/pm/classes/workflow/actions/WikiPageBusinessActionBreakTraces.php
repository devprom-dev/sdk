<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBreakTraceService;
include_once "BusinessActionWorkflow.php";

class WikiPageBusinessActionBreakTraces extends BusinessActionWorkflow
{
 	function getId()
 	{
 		return 'ceed3670-5b35-41ca-82ad-caf7b4af6ea0';
 	}
	
	function apply( $object_it )
 	{
        $service = new WikiBreakTraceService(getFactory());
        $service->execute($object_it);

 		return true;
 	}

 	function getObject()
 	{
 		return null;
 	}
 	
 	function getDisplayName()
 	{
 		return text(2241);
 	}
}
