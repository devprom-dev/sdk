<?php

include 'SubversionPage.php';
include 'SubversionFilesTable.php';
include 'SubversionFileTable.php';
include 'SubversionFileDiffTable.php';
include 'SubversionFileRevisionsSection.php';

class SubversionFilesPage extends SubversionPage
{
    var $subversion_it;
    
    function __construct()
    {
        global $model_factory, $_REQUEST;
    
        $subversion = $model_factory->getObject('pm_Subversion');
        	
        $this->subversion_it = $_REQUEST['subversion'] != '' 
                ? $subversion->getExact($_REQUEST['subversion']) : $subversion->getEmptyIterator();
        	
        parent::__construct();
        	
        if ( $_REQUEST['name'] != '' && $this->subversion_it->getId() > 0 )
        {
            $this->addInfoSection( new SubversionFileRevisionsSection($this->subversion_it)	);
        }
    }
    
 	function getTable() 
 	{
 	    if ( $_REQUEST['name'] != '' )
 	    {
 	        switch ( $_REQUEST['mode'] )
 	        {
     			case 'diff':
     			    return new SubversionFileDiffTable( $this->subversion_it );
    
     			default:
     			    return new SubversionFileTable( $this->subversion_it );
 	        }
 	    }
 	    
		return new SubversionFilesTable();
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getTitle()
 	{
		return text('sourcecontrol3'); 		
 	}
 	
 	function export()
 	{
 		global $model_factory;
	 	
		header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	 	header('Content-Disposition: attachment; filename="'.$_REQUEST['name'].'"');
	 	 
	 	$object = $model_factory->getObject('pm_Subversion');
	 	$object_it = $object->getExact($_REQUEST['subversion']);
	 	
	 	if ( $object_it->getId() > 0 )
	 	{
	 		$connector = $object_it->getConnector();	 	
	 		
	 		echo $connector->getBinaryFile(IteratorBase::wintoutf8($_REQUEST['path']));
	 	}
 	}
}
