<?php

include "ReportCategory.php";
include "PMReportCategoryRegistry.php";
include "predicates/PMReportCategoryPredicate.php";

class PMReportCategory extends ReportCategory
{
	function __construct()
	{
		parent::__construct( new PMReportCategoryRegistry($this) );
	}
}