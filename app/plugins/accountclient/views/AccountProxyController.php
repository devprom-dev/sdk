<?php

include "ProxyForm.php";

class AccountProxyController extends Page
{
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	function getForm()
 	{
 		return new ProxyForm(getFactory()->getObject('License'));
 	}
}
