<?php

include "ScrumForm.php";
include "ScrumTable.php";

class ScrumPage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();
 	}

	function getObject()
	{
 		return getFactory()->getObject('pm_Scrum');
	}
	
 	function getTable() 
 	{
 		return new ScrumTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return new ScrumForm( $this->getObject() );
 	}
}