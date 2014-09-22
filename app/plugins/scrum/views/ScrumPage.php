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
 		global $model_factory;
 		return $model_factory->getObject('pm_Scrum');
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