<?php

include_once "TaskTraceBaseIterator.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageDisplayRules.php";

class TaskTraceWikiPageIterator extends TaskTraceBaseIterator
{
    function getDisplayName()
 	{
 		return WikiPageDisplayRules::getTraceDisplayName($this, $this->getRef($this->getDisplayNameReference()));
 	}
}
