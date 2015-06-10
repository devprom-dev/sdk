<?php

include "SpentTimeIterator.php";
include "SpentTimeRegistry.php";

class SpentTime extends Metaobject
{
 	var $view, $report_year, $report_month, $group;
 	
 	private $participant_filter = array();
 	
 	function __construct() 
 	{
 		parent::__construct('pm_Activity', new SpentTimeRegistry($this) );
 		
 		$this->addAttribute('SystemUser', 'REF_cms_UserId', translate('Пользователь'), false, false);
 	}
 	
 	function createIterator() 
 	{
 		return new SpentTimeIterator( $this );
 	}
 	
 	function setView( $view )
 	{
 		$this->view = $view;
 	}

 	function getView()
 	{
 		return $this->view;
 	}
 	
 	function setGroup( $group )
 	{
 	    $this->group = $group;
 	}
 	
 	function getGroup()
 	{
 	    return $this->group;
 	}
 	
 	function setReportYear( $year )
 	{
 		$this->report_year = is_numeric($year) ? $year : date('Y');
 	}
 	
 	function getReportYear()
 	{
 	    return $this->report_year;
 	}

  	function setReportMonth( $month )
 	{
 		$this->report_month = is_numeric($month) ? $month : date('m');
 	}
 	
 	function getReportMonth()
 	{
 	    return $this->report_month;
 	}
 	
	function setParticipantFilter( array $filters )
	{
		$this->participant_filter = $filters;
	}
 	
	function getParticipantFilter()
	{
		return $this->participant_filter;
	}
	
	function getActivityObject()
	{
		return getFactory()->getObject('pm_Activity');
	}
}
