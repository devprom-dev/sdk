<?php

include "DictionaryList.php";

class DictionaryTable extends StaticPageTable
{
	function getList()
	{
		return new DictionaryList( $this->getObject() );
	}

	function getFilterActions()
	{
		return array();
	}
}