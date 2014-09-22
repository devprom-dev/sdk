<?php

include "TagForm.php";
include "TagTable.php";

class TagPage extends PMPage
{
	function getObject()
	{
		global $model_factory;
		
 		$object = $model_factory->getObject('Tag');
 		
 		$object->extendMetadata();
 		
 		return $object;
	}
	
	function getTable() 
 	{
		return new TagTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return new TagForm();
 	}
}
