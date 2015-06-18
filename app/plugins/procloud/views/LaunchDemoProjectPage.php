<?php

include "LaunchDemoProjectForm.php";

class LaunchDemoProjectPage extends Page
{
	public function __construct()
	{
		parent::__construct();
	}
	
	function needDisplayForm()
	{
	    return true;    
	}
	
	function getForm()
	{
		return new LaunchDemoProjectForm(getFactory()->getObject('User'));
	}
	
	// the page will be available without any authentization required 
 	function authorizationRequired()
 	{
 		return false;
 	}
}
