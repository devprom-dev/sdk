<?php

include SERVER_ROOT_PATH."plugins/sourcecontrol/classes/SCMFileChangeHistory.php";
include 'SubversionPage.php';
include 'SubversionList.php';
include 'ScmFileChangesTable.php';

class ScmFileChangesPage extends SubversionPage
{
 	function __construct()
 	{
 	    parent::__construct();
 	}
 	
 	function getObject()
 	{
 		return new SCMFileChangeHistory();
 	}
 	
 	function getTable() 
 	{
		return new ScmFileChangesTable($this->getObject());
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
}
