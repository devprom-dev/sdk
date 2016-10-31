<?php
include "WorkloadDetailsTable.php";

class WorkloadDetailsPage extends PMPage
{
	function getObject() {
		return getFactory()->getObject('ProjectUser');
	}

	function getTable() {
		return new WorkloadDetailsTable($this->getObject());
	}

	function getForm() {
		return null;
	}

	function getWatchedObjects()
	{
		return array (
			'ChangeLog',
			'User',
			'Task',
			'Request',
			'Iteration',
			'Release'
		);
	}
}

