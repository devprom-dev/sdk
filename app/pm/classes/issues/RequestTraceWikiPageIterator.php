<?php
use Devprom\ProjectBundle\Service\Widget\WidgetService;
include_once "RequestTraceBaseIterator.php";

class RequestTraceWikiPageIterator extends RequestTraceBaseIterator
{
 	function getDisplayName() {
 		return WidgetService::getRequestTraceDisplayName(
 		    $this, $this->getRef($this->getDisplayNameReference()));
 	}
}
