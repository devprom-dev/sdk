<?php

class ActivityReportMonthPredicate extends FilterPredicate
{
 	function _predicate( $filter )
 	{
 	    if ( $filter < 1 || !is_numeric($filter) ) return " AND 1 = 2 ";
 	    
		return " AND MONTH(CONVERT_TZ(t.ReportDate, '".EnvironmentSettings::getUTCOffset().":00', '".EnvironmentSettings::getClientTimeZoneUTC()."')) = ".$filter;
 	}
}
