<?php

include "DateYearWeekRegistry.php";
include "DateYearWeekIterator.php";

class DateYearWeek extends Metaobject
{
	function __construct() 
	{
		return parent::__construct('entity', new DateYearWeekRegistry($this));
	}

	function createIterator()
	{
		return new DateYearWeekIterator($this);
	}
}
