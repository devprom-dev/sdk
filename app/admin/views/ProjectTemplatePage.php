<?php

include ('ProjectTemplateForm.php');
include ('ProjectTemplateTable.php');

class ProjectTemplatePage extends AdminPage
{
	function getTable()
	{
		$object = getFactory()->getObject('pm_ProjectTemplate');
		
		$object->setRegistry( new ObjectRegistrySQL() );
		
		return new ProjectTemplateTable($object);
	}

	function getForm()
	{
		return new ProjectTemplateForm(getFactory()->getObject('pm_ProjectTemplate'));
	}
}
