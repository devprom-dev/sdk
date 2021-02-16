<?php
include "TagForm.php";
include "TagTable.php";

class TagPage extends PMPage
{
	function getObject() {
 		return getFactory()->getObject('Tag');
	}
	
	function getTable() {
		return new TagTable($this->getObject());
 	}
 	
 	function getEntityForm() {
 		return new TagForm($this->getObject());
 	}
}
