<?php
include_once "RequestTraceBase.php";
include_once "RequestTraceWikiPageIterator.php";
include_once "persisters/RequestTraceWikiPageDetailsPersister.php";

class RequestTraceWikiPage extends RequestTraceBase
{
	function __construct()
	{
		parent::__construct();
		$this->addPersister( new RequestTraceWikiPageDetailsPersister() );
	}
	
	function createIterator() {
		return new RequestTraceWikiPageIterator($this);
	}
}
