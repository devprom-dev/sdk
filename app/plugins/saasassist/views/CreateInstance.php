<?php

include "CreateInstanceForm.php";

class CreateInstance extends COPage
{
	function getObject()
	{
		return getFactory()->getObject('entity');
	}
	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}
 	
 	function needDisplayForm()
 	{
 		return true;
 	}
	
	function getForm()
	{
		return new CreateInstanceForm($this->getObject());
	}
}
