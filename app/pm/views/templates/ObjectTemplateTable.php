<?php

include "ObjectTemplateList.php";

class ObjectTemplateTable extends PMPageTable
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
