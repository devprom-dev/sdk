<?php

class ActivityReportYearPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( $filter < 1 || !is_numeric($filter) ) return " AND 1 = 2 ";
 	    
		return " AND YEAR(CONVERT_TZ(t.ReportDate, '".EnvironmentSettings::getUTCOffset().":00', '".EnvironmentSettings::getClientTimeZoneUTC()."')) = ".$filter;
 	}
}
