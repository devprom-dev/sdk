<?php

include "ArtefactTypeForm.php";
include "ArtefactTypeTable.php";

class ArtefactTypePage extends PMPage
{
	function getObject()
	{
 		return getFactory()->getObject('pm_ArtefactType');
	}
	
	function getTable() 
 	{
 		return new ArtefactTypeTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
 		return new ArtefactTypeForm( $this->getObject() );
 	}
}