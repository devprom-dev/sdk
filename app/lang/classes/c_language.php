<?php

include "DateFormatBase.php";
include "DateFormatEuropean.php";
include "DateFormatAmerican.php";
include "DateFormatRussian.php";
 
 ////////////////////////////////////////////////////////////////////////////////// 
 class Language  
 {
 	var $dateformat;
 	var $resource;
 	
 	function __construct()
 	{
		$date_format_constant = 'LANG_DATEFORMAT_'.$this->getLanguage();
		
		if ( defined($date_format_constant) )
		{
			$class_name = constant($date_format_constant);
			
			$this->dateformat = class_exists( $class_name )
				? new $class_name : $this->getDefaultDateFormat();	
		}
		else
		{
			$this->dateformat = $this->getDefaultDateFormat();
		}
 	}
 	
 	function Initialize( $resource = null )
 	{
 	    global $text_data, $model_factory;

 	    $text_data = array();
 	    
 	    if ( !is_object($resource) ) $resource = $model_factory->getObject('cms_Resource');

 	    $resource_it = $resource->getAll();
 	    
 	    $data = $resource_it->getRowset();

 	    foreach( $data as $key => $value )
 	    {
 	        $text_data[$value['ResourceKey']] = $value['ResourceValue'];
 	    }
 	}
 	
 	function getLanguageId() 
 	{
 		return 1;
 	}
 	
 	function getDefaultDateFormat()
 	{
 		return new DateFormatRussian();
 	}
 	
 	function getLanguage() 
 	{
 		return 'RU';
 	}

 	function translate( $text ) 
 	{
 		return $text;
 	}

 	function getLocaleFormatter()
 	{
 		return $this->dateformat;
 	}
 	
 	function getDatepickerFormat() 
 	{
 		return $this->dateformat->getDatepickerFormat();
 	}
 	
 	function getDateFormat() 
 	{
 		return $this->dateformat->getDateFormat();
 	}

 	function getDateFormatShort() 
 	{
 		return $this->dateformat->getDateFormatShort();
 	}
 	
 	function getDateFormatted( $value )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($this->dateformat->getDateFormat(), strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getDateFormattedShort( $value )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($this->dateformat->getDateFormatShort(), strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getDateUserFormatted( $value, $format )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($format, strtotime(SystemDateTime::convertToClientTime($value)));
 	}

 	function getDateTimeFormatted( $value )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($this->dateformat->getDateFormat().' %H:%M', strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getTimeFormatted( $value )
 	{
 		return strftime('%H:%M', strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getPhpDate( $time )
 	{
 		return $this->dateformat->getPhpDate($time); 
 	}

 	function getDbDate( $text )
 	{
		list($year, $month, $day) = explode('-', $text);
		
		if ( $year > 0 && $month > 0 && $day > 0 && checkdate($month, $day, $year) ) return $text;
 		
 		return $this->dateformat->getDbDate($text);
 	}
 	
 	function getDaysWording( $days ) 
 	{
 		if( abs($days) == 1 ) 
 			return '����';
 		elseif( abs($days) > 1 and abs($days) < 5 )
 			return '���';
 		else
 			return '����';
 	}

 	function getWeeksWording( $weeks ) 
 	{
 		if( abs($weeks) == 1 ) 
 			return '������';
 		elseif( abs($weeks) > 1 and abs($weeks) < 5 )
 			return '������';
 		else
 			return '������';
 	}
 	
 	function getDateWording( $db_date )
 	{
 		$today = strtotime(date('Y-m-d'));
 		
		$map = array (
			strftime('%Y-%m-%d', $today) => translate('�������'), 		
			strftime('%Y-%m-%d', strtotime('-1 day', $today)) => translate('�����'), 		
			strftime('%Y-%m-%d', strtotime('-2 day', $today)) => translate('���������'), 		
			strftime('%Y-%m-%d', strtotime('-6 day', $today)) => translate('�� ������'), 		
			strftime('%Y-%m-%d', strtotime('-1 week', $today)) => translate('������ �����'), 		
			strftime('%Y-%m-%d', strtotime('-2 week', $today)) => translate('2 ������ �����')
			);

		foreach( $map as $date => $wording )
		{
			if ( $db_date >= $date ) return $wording;
		}
 	}
 }
 
 ////////////////////////////////////////////////////////////////////////////////// 
 class LanguageEnglish extends Language
 {
 	function getLanguageId()
 	{
 		return 2;
 	}

 	function getLanguage() 
 	{
 		return 'EN';
 	}
 	
 	function getDefaultDateFormat()
 	{
 		return new DateFormatAmerican();
 	}
 	
 	function translate( $text ) 
 	{
 		global $language_translation;
 		
 		$translation = $language_translation[$text];
 		return $translation == '' ? $text : $translation; 
 	}

 	function getDaysWording( $days ) 
 	{
 		if( abs($days) == 1 ) 
 			return 'day';
 		else
 			return 'days';
 	}

 	function getWeeksWording( $weeks ) 
 	{
 		if( abs($weeks) == 1 ) 
 			return 'week';
 		else
 			return 'weeks';
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////
 function getLanguage() 
 { 
 	return getSession()->getLanguage();
 }
 
 ////////////////////////////////////////////////////////////////////////////////// 
 function echo_lang( $text ) 
 {
 	echo translate( $text );
 }

 //////////////////////////////////////////////////////////////////////////////////
 function translate( $text ) 
 {
 	if ( !is_numeric($text) )
 	{
 		return text($text);
 	}
 	else
 	{
 		return $text;
 	}
 } 

 //////////////////////////////////////////////////////////////////////////////////
 function text( $key )
 {
 	global $text_data;

 	//if ( $key == 'ee44' ) echo 'asd';
 	
 	$result = $text_data[$key];
 	
 	return $result != '' ? $result : $key;
 }
 
?>