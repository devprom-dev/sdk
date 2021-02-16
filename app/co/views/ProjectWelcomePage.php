<?php
include "ProjectWelcomeTable.php";

class ProjectWelcomePage extends CoPage
{
 	function getTable() {
		return new ProjectWelcomeTable(getFactory()->getObject('pm_Project'));
 	}

 	function getTitle() {
 		return text('co35');
 	}
}
