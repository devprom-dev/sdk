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

 	function getDateFormatShort( $time )
 	{
 	    if ( defined('DATEFORMAT_SHORT') ) return DATEFORMAT_SHORT;
		if ( strftime('%Y', $time) == date('Y') ) {
			return 'j - '.$this->names_map[date('M', $time)];
		} else {
			return 'j - '.$this->names_map[date('M', $time)].' Y';
		}
 	}
 	
 	function getPhpDate( $time )
 	{
 		return SystemDateTime::convertToClientTime(date('Y-m-d H:i:s', $time), 'j.m.Y');
 	}

    function getPhpDateTime( $time )
    {
        return date('j.m.Y H:i:s', $time);
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

 	function getDaysWording( $days )
 	{
 		if( abs($days) == 1 ) 
 			return 'день';
 		elseif( abs($days) > 1 and abs($days) < 5 )
 			return 'дня';
 		else
 			return 'дней';
 	}

	private $names_map = array(
		'Jan' => 'Янв',
		'Feb' => 'Февр',
		'Mar' => 'Март',
		'Apr' => 'Апр',
		'May' => 'Май',
		'Jun' => 'Июнь',
		'Jul' => 'Июль',
		'Aug' => 'Авг',
		'Sep' => 'Сент',
		'Oct' => 'Окт',
		'Nov' => 'Нояб',
		'Dec' => 'Дек',
	);

    function getExcelDateFormat() {
        return 'dd.mm.yyyy';
    }

    function getExcelDateTimeFormat() {
        return 'dd.mm.yyyy h:mm:ss';
    }
}