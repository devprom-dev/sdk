<?php

include "TagList.php";

class TagTable extends PMPageTable
{
	function getList()
	{
		return new TagList( $this->getObject() );
	}

	function getFilterActions()
	{
		return array();
	}
	
	function IsNeedToAdd()
	{
		return false;
	}
} 
