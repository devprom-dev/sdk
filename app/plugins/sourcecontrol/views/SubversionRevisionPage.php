<?php

include 'SubversionPage.php';
include 'SubversionList.php';
include 'SubversionRevisionTable.php';
include 'SubversionRevisionDetailsTable.php';
include "SubversionRevisionPageSettingsBuilder.php";

class SubversionRevisionPage extends SubversionPage
{
 	var $subversion;
 	
 	function __construct()
 	{
 	    getSession()->addBuilder( new SubversionRevisionPageSettingsBuilder() );
 		
 	    parent::__construct();
 	}
 	
 	function getObject()
 	{
 		return getFactory()->getObject('pm_SubversionRevision');
 	}
 	
 	function getTable() 
 	{
 		switch ( $_REQUEST['mode'] )
 		{
 			case 'details':
 				return new SubversionRevisionDetailsTable($this->getObject());

 			default:
 				return new SubversionRevisionTable($this->getObject());
 		}
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getTitle()
 	{
		return text('sourcecontrol4'); 		
 	}
}
