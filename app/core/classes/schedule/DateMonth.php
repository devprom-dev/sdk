<?php

include "DateMonthRegistry.php";

class DateMonth extends MetaobjectCacheable
{
	function __construct() 
	{
		parent::__construct('entity', new DateMonthRegistry($this));
	}
}
