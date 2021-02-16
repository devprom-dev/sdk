<?php
include 'PluginTable.php';

class PluginPage extends AdminPage
{
	function getObject() {
		return getFactory()->getObject('pm_Project');
	}

	function getTable() {
		return new PluginTable($this->getObject());
	}

	function getEntityForm() {
		return null;
	}
}
