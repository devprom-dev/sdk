<?php
include "RegisterToCourseForm.php";

class RegisterToCoursePage extends Page
{
	public function __construct()
	{
		parent::__construct();
	}
	
	// returns Table object to render the list of data
 	function getForm()
 	{
 		return new RegisterToCourseForm(getFactory()->getObject('cms_User'));
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
}
