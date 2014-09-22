<?php

class DateFormatRussian extends DateFormatBase
{
	function getDisplayName()
	{
		return 'RU: dd.mm.yyyy';
	}
	
 	function getDatepickerFormat()
 	{
 		return 'dd.mm.yy';
 	}
	
 	function getDatepickerLanguage()
 	{
 		return 'ru';
 	}
 	
 	function getDateFormat() 
 	{
 		return '%d.%m.%Y';
 	}

 	function getDateFormatShort() 
 	{
 		return '%d.%m.%y';
 	}
 	
 	function getPhpDate( $time )
 	{
 		return SystemDateTime::convertToClientTime(date('Y-m-d H:i:s', $time), 'j.m.Y');
 	}

 	function getDateJSFormat()
 	{
 		return 'dd.MM.yyyy';
 	}
 	
 	function getDbDate( $text )
 	{
		list($day, $month, $year) = explode('.', $text);

		if ( $year < 1 || $month < 1 || $day < 1 ) return "";
		
		if ( !checkdate($month, $day, $year) ) return "";
		
		return $year."-".$month."-".$day;
 	}
}
