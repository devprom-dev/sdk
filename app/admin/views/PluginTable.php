<?php

include ('PluginList.php');

class PluginTable extends StaticPageTable
{
	function getList()
	{
		return new PluginList( $this->getObject() );
	}

	function getCaption()
	{
		return '';
	}

	function getFilterActions()
	{
		return array();
	}
}
