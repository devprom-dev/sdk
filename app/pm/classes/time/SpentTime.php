<?php
include "SpentTimeIterator.php";
include "SpentTimeRegistry.php";
include "predicates/SpentTimeStatePredicate.php";

class SpentTime extends Metaobject
{
 	private $report_year;
 	private $report_month;
 	private $group;
 	private $participant_filter = array();
 	private $rowsObject = null;
 	
 	function __construct() 
 	{
 		parent::__construct('pm_Activity', new SpentTimeRegistry($this) );
 		
 		$this->addAttribute('SystemUser', 'REF_cms_UserId', translate('Пользователь'), false, false);
		$this->removeAttribute('Iteration');
 	}
 	
 	function createIterator() 
 	{
 		return new SpentTimeIterator( $this );
 	}
 	
 	function setGroup( $group )
 	{
 	    $this->group = $group;
 	}
 	
 	function getGroup()
 	{
 	    return $this->group;
 	}

 	function setRowsObject( $object ) {
 	    $this->rowsObject = $object;
    }

    function getRowsObject() {
 	    return $this->rowsObject;
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

	function DeletesCascade($object)
	{
		return false;
	}

	function IsUpdatedCascade($object)
	{
		return false;
	}

	function IsDeletedCascade($object)
	{
		return false;
	}
}
