<?php

include "LicenseForm.php";

class AccountFormController extends Page
{
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}
 	
 	function getForm()
 	{
 		return new LicenseForm(getFactory()->getObject('License'));
 	}
}
