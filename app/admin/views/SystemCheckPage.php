<?php

include ('SystemCheckTable.php');

class SystemCheckPage extends AdminPage
{
	function getObject()
	{
		return getFactory()->getObject('SystemCheck');
	}

	function getTable()
	{
		return new SystemCheckTable( $this->getObject() );
	}

	function getEntityForm()
	{
		return null;
	}
}