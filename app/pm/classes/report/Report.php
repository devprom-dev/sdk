<?php
include "ReportRegistry.php";

class Report extends MetaobjectCacheable
{
 	function __construct( ReportRegistry $registry = null ) {
 		parent::__construct('cms_Report',
            is_object($registry) ? $registry : new ReportRegistry($this) );
 	}
 	
	function getCacheCategory() {
	    return getSession()->getCacheKey( $this->getVpdValue() );
	}
}

