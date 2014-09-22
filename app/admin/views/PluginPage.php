<?php

include ('PluginTable.php');

class PluginPage extends AdminPage
{
	function getObject()
	{
		global $model_factory;
		return $model_factory->getObject('pm_Project');
	}

	function getTable()
	{
		return new PluginTable($this->getObject());
	}

	function getForm()
	{
		return null;
	}
}
