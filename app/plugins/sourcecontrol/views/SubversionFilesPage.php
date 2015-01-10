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
        $this->subversion_it = $_REQUEST['subversion'] != '' 
                ? $this->getObject()->getExact($_REQUEST['subversion']) 
        		: $this->getObject()->getEmptyIterator();
        	
        parent::__construct();
        	
        if ( $_REQUEST['name'] != '' && $this->subversion_it->getId() > 0 )
        {
            $this->addInfoSection( new SubversionFileRevisionsSection($this->subversion_it)	);
        }
    }
    
    function getObject()
    {
    	return getFactory()->getObject('pm_Subversion');
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
     			    return new SubversionFileTable( $this->getObject() );
 	        }
 	    }
 	    
		return new SubversionFilesTable();
 	}
 	
    function getRenderParms()
    {
    	$parms = parent::getRenderParms();
    	
    	if ( $_REQUEST['name'] == '' || $_REQUEST['mode'] == 'diff' ) return $parms;
    	
    	$file_body = $this->subversion_it->getConnector()->getTextFile(
			        		IteratorBase::wintoutf8($_REQUEST['path']),
    						$_REQUEST['version']
			         );
    	
        $file_name = tempnam(sys_get_temp_dir(), md5($_REQUEST['name']));
    	file_put_contents($file_name, $file_body);
    	
    	$finfo = new finfo(FILEINFO_MIME);
    	$content_type = $finfo->file($file_name);
    	
    	if ( strpos($content_type, 'text') === false )
    	{
    		// binary file 	
			header("Expires: Thu, 1 Jan 1970 00:00:00 GMT"); // Date in the past
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
			header('Content-Type: '.$content_type);
		 	header('Content-Disposition: attachment; filename="'.$_REQUEST['name'].'"');
    		
		 	echo $file_body;
		 	die();
    	}
    	
    	return array_merge( $parms,
        		array (
		            'file_body' => IteratorBase::utf8towin($file_body),
		            'path' => $_REQUEST['path'],
		            'name' => IteratorBase::utf8towin($_REQUEST['name']),
		            'version' => $_REQUEST['version'],
        		)
        );
    }
 	
 	function getForm() 
 	{
 		return null;
 	}
 	
 	function getTitle()
 	{
		return text('sourcecontrol3'); 		
 	}

 	function getHint()
 	{
 		if ( $_REQUEST['name'] != '' ) return '';
 		
 		return parent::getHint();
 	}
}
