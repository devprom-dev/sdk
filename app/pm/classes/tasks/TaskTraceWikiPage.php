<?php
include_once "TaskTraceBase.php";
include_once "TaskTraceWikiPageIterator.php";
include_once "persisters/TaskTraceWikiPageDetailsPersister.php";

class TaskTraceWikiPage extends TaskTraceBase
{
	function __construct()
	{
		parent::__construct();
		
		$this->addPersister( new TaskTraceWikiPageDetailsPersister() );
	}
	
	function createIterator()
	{
		return new TaskTraceWikiPageIterator($this);
	}
}