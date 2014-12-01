<?php
include_once "ContextResourceRegistry.php";

class ContextResource extends MetaobjectCacheable
{
 	function __construct() 
 	{
 		parent::__construct('cms_Resource', new ContextResourceRegistry($this));
 	}
}