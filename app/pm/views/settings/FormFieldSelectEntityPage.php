<?php
include "FormFieldEntity.php";

class FormFieldSelectEntityPage extends PMPage
{
	function getObject() {
 		return getFactory()->getObject('StateAttribute');
	}
    
    function needDisplayForm() {
 		return true;
 	}
 	
 	function getEntityForm() {
 	    return new FormFieldEntity($this->getObject());
 	}
}
