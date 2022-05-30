<?php
include "SelectProjectForm.php";

class SelectProjectPage extends PMPage
{
	function getObject() {
 		return getFactory()->getObject('cms_Language');
	}
    
    function needDisplayForm() {
 		return true;
 	}
 	
 	function getEntityForm() {
 	    return new SelectProjectForm($this->getObject());
 	}
}
