<?php
include_once SERVER_ROOT_PATH."admin/methods/UnBlockUserWebMethod.php";
include ('BlacklistTable.php');

class BlackPage extends AdminPage
{
	function getObject()
	{
		return getFactory()->getObject('cms_BlackList');
	}
	
	function getTable()
	{
		return new BlackTable($this->getObject());
	}

	function getEntityForm()
	{
		return null;
	}
}
