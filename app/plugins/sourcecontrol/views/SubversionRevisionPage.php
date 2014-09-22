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
 	    parent::__construct();
 	    
 	    getSession()->addBuilder( new SubversionRevisionPageSettingsBuilder() );
 	}
 	
 	function getObject()
 	{
 		global $model_factory;
 		return $model_factory->getObject('pm_SubversionRevision');
 	}
 	
 	function getTable() 
 	{
 		global $_REQUEST, $model_factory;
 		
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
 	
 	function export()
 	{
 		global $model_factory;
	 	
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	 	header('Content-Disposition: attachment; filename="'.$_REQUEST['name'].'"');
	 	 
	 	if ( $_REQUEST['subversion'] > 0 )
	 	{
	 	    $object = $model_factory->getObject('pm_Subversion');
	 	    
	 	    $object_it = $object->getExact($_REQUEST['subversion']);
	 	     
	 	    if ( $object_it->getId() > 0 )
	 	    {
	 	        $connector = $object_it->getConnector();
	 	    
	 	        echo $connector->getBinaryFile(IteratorBase::wintoutf8($_REQUEST['path']), $_REQUEST['version']);
	 	        
	 	        return;
	 	    }
	 	}
	 	
	 	parent::export();
 	}
}
