<?php

include "DeliveryChartTable.php";

class DeliveryPage extends PMPage
{
 	function __construct()
 	{
 		parent::__construct();
 	}
 	
	function getObject()
	{
		return getFactory()->getObject('DeliveryChartData');
	}
	
 	function getTable() 
 	{
 		return new DeliveryChartTable( $this->getObject() );
 	}
 	
 	function getEntityForm()
 	{
 		return null;
 	}
}