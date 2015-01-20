<?php

include "ProxyForm.php";

class AccountProxyController extends Page
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
 		return new ProxyForm(getFactory()->getObject('License'));
 	}
}
