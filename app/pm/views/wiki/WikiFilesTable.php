<?php

include "FilesWikiPagesList.php";

class WikiFilesTable extends PMPageTable
{
	function getList()
	{
		return new FilesWikiPagesList( $this->getObject() );
	}

	function getNewActions()
	{
		return array();
	}
	
	function getDeleteActions()
	{
		return array();
	}
	
	function getCaption()
	{
	    return text(809);
	}
	
	function getSortDefault( $sort )
	{
		return $sort == 'sort' ? 'RecordModified.D' : "none";
	}
} 
