<?php

include "DeadlineSwimlaneRegistry.php";

class DeadlineSwimlane extends MetaobjectCacheable
{
	function __construct() 
	{
		parent::__construct('entity', new DeadlineSwimlaneRegistry($this));
	}
}
