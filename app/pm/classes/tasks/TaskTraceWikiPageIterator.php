<?php
use Devprom\ProjectBundle\Service\Widget\WidgetService;
include_once "TaskTraceBaseIterator.php";

class TaskTraceWikiPageIterator extends TaskTraceBaseIterator
{
    function getDisplayName() {
 		return WidgetService::getTraceDisplayName(
 		    $this, $this->getRef($this->getDisplayNameReference()));
 	}
}
