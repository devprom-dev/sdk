<?php

include ('ProjectTemplateForm.php');
include ('ProjectTemplateTable.php');

class ProjectTemplatePage extends AdminPage
{
	function getObject()
	{
		$object = getFactory()->getObject('pm_ProjectTemplate');
		$object->setRegistry( new ObjectRegistrySQL() );
		return $object;
	}
	
	function getTable()
	{
		return new ProjectTemplateTable($this->getObject());
	}

	function getForm()
	{
		return new ProjectTemplateForm($this->getObject());
	}
}
