<?php
include "ShareModuleSettingsForm.php";

class ShareModuleSettingsPage extends PMPage
{
	function getObject()
	{
 		return getFactory()->getObject('cms_Language');
	}
    
    function needDisplayForm()
 	{
 		return true;
 	}
 	
 	function getForm() 
 	{
 	    return new ShareModuleSettingsForm($this->getObject());
 	}
}
