<?php

include "DictionaryList.php";

class DictionaryTable extends PMPageTable
{
	function getList()
	{
		return new DictionaryList( $this->getObject() );
	}

	function getFilterActions() 
	{
		return array();
	}
	
	function getNewActions()
	{
	    return array();
	}
}
