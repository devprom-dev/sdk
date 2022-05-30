<?php
include_once "Report.php";
include "PMReportRegistry.php";
include "PMReportIterator.php";

class PMReport extends Report
{
 	function __construct()
 	{
 		parent::__construct( new PMReportRegistry($this) );
		$this->addAttributeGroup('Category', 'system');
    }
 	
 	function createIterator() {
 	    return new PMReportIterator( $this );
 	}
 	
 	function getByModule( $module_uid )
 	{
 	    $it = $this->getAll();
 	    $it->moveTo('Module', $module_uid);
 	    return $it->get('Module') == $module_uid ? $it->getCurrentIt() : $this->getEmptyIterator();
 	}
}