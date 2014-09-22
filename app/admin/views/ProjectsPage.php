<?php

include ('ProjectTable.php');

class ProjectsPage extends AdminPage
{
	function getObject()
	{
		return getFactory()->getObject('pm_Project');
	}
	
	function getTable()
	{
		return new ProjectTable($this->getObject());
	}

	function getForm()
	{
		return null;
	}
}
