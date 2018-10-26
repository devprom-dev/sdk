<?php

include "ArtefactTable.php";
include "ArtefactForm.php";

class ArtefactPage extends PMPage
{
	function getObject()
	{
 		return getFactory()->getObject('pm_Artefact');
	}
 	
 	function getTable() 
 	{
	 	return new ArtefactTable( $this->getObject() );
 	}
 	
 	function needDisplayForm()
	{
		return in_array($_REQUEST['mode'], array('bulk')) || parent::needDisplayForm();
	}
 	
 	function getForm() 
 	{
		return new ArtefactForm( $this->getObject() );
 	}
}
 