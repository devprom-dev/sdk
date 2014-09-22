<?php

include ('ProjectTemplateList.php');

class ProjectTemplateTable extends PageTable
{
	function getList()
	{
		return new ProjectTemplateList( $this->getObject() );
	}

	function getFilterActions()
	{
		return array();
	}
}
