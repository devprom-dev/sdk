<?php
include "WatchingsList.php";

class WatchingsTable extends PMPageTable
{
	function getObject() {
 		return $this->getFamilyModules()->getObject('pm_Watcher');
	}
	
	function getList()
	{
		return new WatchingsList( $this->getObject() );
	}

	function getCaption() 
	{
		return translate('Объекты под наблюдением');
	}
	
	function getNewActions()
	{
	    return array();
	}
} 