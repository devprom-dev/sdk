<?php

include "PMCustomReportIterator.php";
include "predicates/CustomReportMyPredicate.php";
include "CustomReportMyRegistry.php";

class PMCustomReport extends Metaobject
{
 	function __construct( $registry = null ) 
 	{
 		parent::__construct('pm_CustomReport', $registry);
 		
		$this->setAttributeVisible( 'IsHandAccess', false );
		
		$this->setAttributeRequired( 'Url', false );
 	}
 	
 	function createIterator() 
 	{
 		return new PMCustomReportIterator( $this );
 	}
 	
 	function getMyRegistry()
 	{
 		return new CustomReportMyRegistry($this);
 	}
 	
	function getPage() 
	{
		return getSession()->getApplicationUrl().'project/reports?';
	}
	
	function getPageNameObject( $object_id = '', $report_it = null )
	{
		$area_it = getFactory()->getObject('FunctionalArea')->getAll();
		
	    $url = str_replace(
	            $this->getPage(), 
	            trim($this->getPage(),'?').'/'.$area_it->getId().'?', 
	            parent::getPageNameObject($object_id)
	    );

	    if ( $report_it instanceof PMReportIterator )
	    {
	        $url .= '&Category='.$area_it->getId().'&ReportBase='.$report_it->getId();
	    }
	    
		if ( $report_it instanceof ModuleIterator )
	    {
	        $url .= '&Category='.$area_it->getId().'&Module='.$report_it->getId();
	    }
	    
	    return $url;
	}
	
	function getDefaultAttributeValue( $name ) 
	{
		switch ( $name )
		{
			case 'Author':
				return getSession()->getUserIt()->getId();

			default:
				return parent::getDefaultAttributeValue($name);
		}
	}
}