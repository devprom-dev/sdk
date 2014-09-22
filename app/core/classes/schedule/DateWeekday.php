<?php

include_once "DateWeekdayRegistry.php";

class DateWeekday extends MetaobjectCacheable
{
	function __construct() 
	{
		return parent::__construct('entity', new DateWeekdayRegistry($this));
	}
}
