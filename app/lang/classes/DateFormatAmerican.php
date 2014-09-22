<?php

class DateFormatAmerican extends DateFormatBase
{
	function getDisplayName()
	{
		return 'US: mm/dd/yyyy';
	}
	
 	function getDatepickerFormat()
 	{
 		return 'mm/dd/yy';
 	}
 	
 	function getDatepickerLanguage()
 	{
 		return 'en-US';
 	}
	
 	function getDateFormat() 
 	{
 		return '%m/%d/%Y';
 	}
 	
 	function getDateFormatShort() 
 	{
 		return '%m/%d/%y';
 	}
 	
 	function getDateJSFormat()
 	{
 		return 'MM/dd/yyyy';
 	}
 	
 	function getPhpDate( $time )
 	{
 		return SystemDateTime::convertToClientTime(date('Y-m-d H:i:s', $time), 'm/j/Y'); 
 	}

 	function getDbDate( $text )
 	{
		list($month, $day, $year) = explode('/', $text);
		
		if ( $year < 1 || $month < 1 || $day < 1 ) return "";
		
		if ( !checkdate($month, $day, $year) ) return '';
		
		return $year."-".$month."-".$day;
 	}
}