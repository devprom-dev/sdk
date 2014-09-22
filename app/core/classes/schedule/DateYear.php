<?php

include_once "DateYearRegistry.php";

class DateYear extends MetaobjectCacheable
{
	function __construct() 
	{
		parent::__construct('entity', new DateYearRegistry($this));
	}
}
