<?php
include "ObjectTemplateList.php";

class ObjectTemplateTable extends SettingsTableBase
{
	function getList()
	{
		return new ObjectTemplateList( $this->object );
	}

	function IsNeedToAdd() 
	{
		return false;
	}
} 
