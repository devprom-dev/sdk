<?php

include "ScriptRegistry.php";

class Script extends MetaobjectCacheable
{
 	function __construct()
 	{
 		parent::__construct('entity', new ScriptRegistry($this));
 	}
}

