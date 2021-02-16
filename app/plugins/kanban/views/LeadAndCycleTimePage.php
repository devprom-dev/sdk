<?php
include "LeadAndCycleTimeTable.php";
include dirname(__FILE__)."/../classes/LeadCycleTimeModelBuilder.php";

class LeadAndCycleTimePage extends PMPage
{
	function getObject()
	{
	    getSession()->addBuilder( new LeadCycleTimeModelBuilder() );
 		return getFactory()->getObject('pm_ChangeRequest');
	}
 	
 	function getTable() {
 		return new LeadAndCycleTimeTable($this->getObject());
 	}
 	
 	function getEntityForm() {
 		return null;
 	}
}