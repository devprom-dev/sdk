<?php

include "ReportCategoryRegistry.php";

class ReportCategory extends MetaobjectCacheable
{
 	function __construct( ReportCategoryRegistry $registry = null )
 	{
 		parent::__construct('cms_ReportCategory', is_object($registry) ? $registry : new ReportCategoryRegistry($this));
 	}
}
