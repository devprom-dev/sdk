<?php

include ('ProjectCreateForm.php');
 
class CreateProjectPage extends CoPage
{
	function getObject() {
		return getFactory()->getObject('pm_Project');
	}

 	function getTable() {
		return new CreateProjectForm($this->getObject());
 	}

	function hasAccess() {
		return getFactory()->getAccessPolicy()->can_create($this->getObject());
	}
}
