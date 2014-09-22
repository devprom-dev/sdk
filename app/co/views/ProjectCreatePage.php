<?php

include ('ProjectCreateForm.php');
 
class CreateProjectPage extends CoPage
{
 	function getTable()
 	{
 		global $model_factory;
 		
		return new CreateProjectForm(
			$model_factory->getObject('pm_Project'));
 	}
}
