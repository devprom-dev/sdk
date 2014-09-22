<?php

include "ModuleCategoryRegistry.php";

class ModuleCategory extends MetaobjectCacheable
{
 	function __construct()
 	{
 		parent::__construct('entity', new ModuleCategoryRegistry());
 	}
}

