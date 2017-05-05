<?php
include 'ProjectTable.php';
include "ProjectForm.php";

class ProjectsPage extends AdminPage
{
	function getObject() {
		return getFactory()->getObject('pm_Project');
	}
	
	function getTable() {
		return new ProjectTable($this->getObject());
	}

	function getForm() {
		return new ProjectForm($this->getObject());
	}
}
