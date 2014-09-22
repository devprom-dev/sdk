<?php

class DateFormatEuropean extends DateFormatBase
{
	function getDisplayName()
	{
		return 'GB: dd/mm/yyyy';
	}
	
 	function getDatepickerFormat()
 	{
 		return 'dd/mm/yy';
 	}
	
 	function getDatepickerLanguage()
 	{
 		return 'en-GB';
 	}
 	
 	function getDateFormat() 
 	{
 		return '%d/%m/%Y';
 	}

 	function getDateFormatShort() 
 	{
 		return '%d/%m/%y';
 	}
 	
 	function getPhpDate( $time )
 	{
 		return SystemDateTime::convertToClientTime(date('Y-m-d H:i:s', $time), 'm/j/Y'); 
 	}

 	function getDateJSFormat()
 	{
 		return 'dd/MM/yyyy';
 	}
 	
 	function getDbDate( $text )
 	{
		list($day, $month, $year) = explode('/', $text);
		
		if ( $year < 1 || $month < 1 || $day < 1 ) return "";
		
		if ( !checkdate($month, $day, $year) ) return '';
		
		return $year."-".$month."-".$day;
 	}
}
