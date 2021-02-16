<?php
include "DateYearQuarterRegistry.php";
include "DateYearQuarterIterator.php";

class DateYearQuarter extends Metaobject
{
	function __construct() 
	{
		return parent::__construct('entity', new DateYearQuarterRegistry($this));
	}

	function createIterator()
	{
		return new DateYearQuarterIterator($this);
	}
}
