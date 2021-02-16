<?php

include 'TextChangesChartTable.php';

class TextChangesChartPage extends PMPage
{
 	function getObject()
 	{
 		return new TextChangeHistory();
 	}

	function getPredicates()
	{
		return array();
	}

 	function getTable() 
 	{
		return new TextChangesChartTable($this->getObject());
 	}
 	
 	function getEntityForm()
 	{
 		return null;
 	}
}
