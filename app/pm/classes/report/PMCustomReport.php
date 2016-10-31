<?php

include "PMCustomReportIterator.php";
include "predicates/CustomReportMyPredicate.php";
include "predicates/CustomReportCommonPredicate.php";
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

	function getSaveUrl($object_id = '', $report_it = null)
	{
		$url = str_replace(
			$this->getPage(),
			trim($this->getPage(),'?').'/favs?',
			$this->getPageNameObject($object_id)
		);

		if ( $report_it instanceof PMReportIterator ) {
			$url .= '&Category=favs&ReportBase='.$report_it->getId();
		}

		if ( $report_it instanceof ModuleIterator ) {
			$url .= '&Category=favs&Module='.$report_it->getId();
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