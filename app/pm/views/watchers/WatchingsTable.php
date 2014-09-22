<?php

include "WatchingsList.php";

class WatchingsTable extends PMPageTable
{
	function getObject()
	{
		global $model_factory;
 		return $model_factory->getObject('pm_Watcher');
	}
	
	function getList()
	{
		return new WatchingsList( $this->getObject() );
	}

	function getCaption() 
	{
		return translate('Объекты под наблюдением');
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