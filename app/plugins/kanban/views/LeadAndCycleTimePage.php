<?php

include "LeadAndCycleTimeTable.php";
include dirname(__FILE__)."/../classes/LeadCycleTimeModelBuilder.php";

class LeadAndCycleTimePage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();
 	}
 	
	function getObject()
	{
	    getSession()->addBuilder( new LeadCycleTimeModelBuilder() );
	    
 		$object = getFactory()->getObject('pm_ChangeRequest');
 		$object->addFilter( new StatePredicate('terminal') );
 		
 		return $object;
	}
 	
 	function getTable() 
 	{
 		return new LeadAndCycleTimeTable($this->getObject());
 	}
 	
 	function getForm() 
 	{
 		return null;
 	}
}