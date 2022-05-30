<?php
include "ReportTable.php";
include "PMCustomReportForm.php";

class ReportPage extends PMPage
{
	function getObject() {
		return getFactory()->getObject('PMReport');
	}
	
	function getTable() {
 		return new ReportTable( $this->getObject() );
 	}

 	function getEntityForm() {
 		return new PMCustomReportForm();
 	}
}