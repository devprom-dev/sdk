<?php

include "DictionaryList.php";

class DictionaryTable extends PMPageTable
{
	function getObject()
	{
		global $model_factory;
 		return $model_factory->getObject('Dictionary');
	}
	
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
	
	function drawFooter()
	{
	}
} 
