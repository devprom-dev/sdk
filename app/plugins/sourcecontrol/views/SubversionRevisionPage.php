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
 		$title = parent::getTitle();
 		
 		if ( $title != '' ) return $title;
 		
		return text('sourcecontrol4'); 		
 	}
 	
 	function getHint()
 	{
 		if ( $_REQUEST['mode'] != '' ) return '';
 		if ( $this->getReport() != '' )
 		{
 			if ( getFactory()->getObject('PMReport')->getExact($this->getReport())->get('Type') == 'chart' ) return '';
 		}
 		
 		return parent::getHint();
 	}
}
