<?php

include "StyleSheetRegistry.php";

class StyleSheet extends MetaobjectCacheable
{
 	function __construct()
 	{
 		parent::__construct('entity', new StyleSheetRegistry($this));
 	}
}

