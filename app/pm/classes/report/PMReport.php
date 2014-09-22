<?php

include_once "Report.php";
include "PMReportRegistry.php";
include "PMReportIterator.php";

class PMReport extends Report
{
 	var $section, $system, $users;
 	
 	function __construct()
 	{
 	    global $model_factory;
 	    
 		$this->system = false;
 		$this->users = false;
 		
 		parent::__construct( new PMReportRegistry($this) );
 	}
 	
 	function setSystemOnly()
 	{
 		$this->system = true;
 	}
 	
 	function setUsersOnly()
 	{
 	    $this->users = true;    
 	}
 	
 	function resetSystemOnly()
 	{
 		$this->system = false;
 	}

 	function getSystemOnly()
 	{
 		return $this->system;
 	}

 	function getUsersOnly()
 	{
 	    return $this->users;
 	}
 	
 	function createIterator()
 	{
 	    return new PMReportIterator( $this );
 	}
 	
 	function getByModule( $module_uid )
 	{
 	    $it = $this->getAll();
 	    
 	    $it->moveTo('Module', $module_uid);
 	    
 	    return $it->get('Module') == $module_uid ? $it->getCurrentIt() : $this->getEmptyIterator(); 
 	}
}