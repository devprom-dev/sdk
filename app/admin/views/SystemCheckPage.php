<?php

include ('SystemCheckTable.php');

class SystemCheckPage extends AdminPage
{
	function getObject()
	{
		global $model_factory;
		return $model_factory->getObject('SystemCheck');
	}

	function getTable()
	{
		return new SystemCheckTable( $this->getObject() );
	}

	function getForm()
	{
		return null;
	}
}