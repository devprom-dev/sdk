<?php

include "FailedForm.php";

class FailedController extends Page
{
 	function needDisplayForm() 
 	{
 		return true;
 	}
 	
 	function getForm()
 	{
 		return new FailedForm(getFactory()->getObject('License'));
 	}
}
