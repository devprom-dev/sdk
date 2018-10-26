<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBreakTraceService;
include_once "BusinessActionShift.php";

class WikiPageBusinessActionShiftBreakTraces extends BusinessActionShift
{
 	function getId()
 	{
 		return '1e2cc9bc-9aa6-4d0f-9c39-b972a980fbf3';
 	}
	
	function applyContent( $object_it, $attributes, $action = '' )
 	{
        if ( !in_array('Content', $attributes) ) return true;

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
 		return text(2240);
 	}
}
