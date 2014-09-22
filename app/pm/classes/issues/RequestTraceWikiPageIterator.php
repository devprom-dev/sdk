<?php

include_once "RequestTraceBaseIterator.php";
include_once SERVER_ROOT_PATH."pm/classes/wiki/WikiPageDisplayRules.php";

class RequestTraceWikiPageIterator extends RequestTraceBaseIterator
{
 	function getDisplayName()
 	{
 		return WikiPageDisplayRules::getTraceDisplayName($this, $this->getRef($this->getDisplayNameReference()));
 	}
}
