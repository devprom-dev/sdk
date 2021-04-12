<?php

include_once SERVER_ROOT_PATH."pm/classes/model/classes.php";
include "WorkTable.php";

class WorkTablePage extends Page
{
	public function __construct()
	{
		parent::__construct();
	}
	
	// returns Table object to render the list of data
 	function getTable() 
 	{
 		getSession()->addBuilder( new RequestWorkTableMetadataBuilder() );
 		
 		return new WorkTable( getFactory()->getObject('Request') );
 	}
 	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}
}
