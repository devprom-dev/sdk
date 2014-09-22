<?php

include ('BlacklistList.php');

class BlackTable extends PageTable
{
	function getList()
	{
		$this->object->defaultsort = 'RecordCreated DESC';
		return new BlackList( $this->object );
	}

	function getTablePageUrl()
	{
		return 'blacklist.php';
	}

	function getFilterActions()
	{
		return array();
	}

	function getNewActions()
	{
		return array();
	}

	function getDeleteActions()
	{
		return array();
	}
}
