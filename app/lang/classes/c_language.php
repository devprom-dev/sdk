<?php

include "DateFormatBase.php";
include "DateFormatEuropean.php";
include "DateFormatAmerican.php";
include "DateFormatRussian.php";
include_once SERVER_ROOT_PATH.'core/classes/system/CacheLock.php';

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
 	    global $text_data;
 	    if ( !is_object($resource) ) $resource = getFactory()->getObject('cms_Resource');
 	    
 	    $cache_path = $this->getCacheFilePath($resource);
 	    if ( file_exists($cache_path) ) {
 	    	$text_data = include($cache_path);
 	    	return;
 	    }

		$this->buildCache($resource, $cache_path);
		$text_data = include($cache_path);
		return;
 	}
 	
 	protected function buildCache($resource, $cache_path)
 	{
		$lock = new CacheLock();

 		$records = array();
 	    $resource_it = $resource->getAll();
 	    $data = $resource_it->getRowset();
 	    foreach( $data as $key => $value )
 	    {
 	    	$records[] = "'".$value['ResourceKey']."' => '".preg_replace("/'/", "\\'", $value['ResourceValue'])."'"; 
 	    }
 	    @mkdir(dirname($cache_path), 0777, true);
 	    file_put_contents($cache_path, '<?php return array('.join(',',$records).');');
 	}
 	
 	protected function getCacheFilePath($resource)
 	{
 		return CACHE_PATH.'/appcache/'.
 				getFactory()->getEntityOriginationService()->getCacheCategory($resource).
 					'/texts.php';
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

 	function getDateFormatted( $value )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($this->dateformat->getDateFormat(), strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getDateFormattedShort( $value )
 	{
 		if ( $value == '' ) return $value;
 		return SystemDateTime::convertToClientTime($value, $this->dateformat->getDateFormatShort(strtotime($value)));
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
		$time = strtotime($text);
		if ( $time === false ) return $this->dateformat->getDbDate($text);
 		return strftime("%Y-%m-%d", $time);
 	}
 	
 	function getDaysWording( $days ) 
 	{
 		return $this->dateformat->getDaysWording($days);
 	}

 	function getWeeksWording( $weeks ) 
 	{
 		if( abs($weeks) == 1 ) 
 			return 'неделя';
 		elseif( abs($weeks) > 1 and abs($weeks) < 5 )
 			return 'недели';
 		else
 			return 'недель';
 	}
 	
 	function getDateWording( $db_date )
 	{
 		$today = strtotime(date('Y-m-d'));
 		
		$map = array (
			strftime('%Y-%m-%d', $today) => translate('сегодня'), 		
			strftime('%Y-%m-%d', strtotime('-1 day', $today)) => translate('вчера'), 		
			strftime('%Y-%m-%d', strtotime('-2 day', $today)) => translate('позавчера'), 		
			strftime('%Y-%m-%d', strtotime('-6 day', $today)) => translate('на неделе'), 		
			strftime('%Y-%m-%d', strtotime('-1 week', $today)) => translate('неделю назад'), 		
			strftime('%Y-%m-%d', strtotime('-2 week', $today)) => translate('2 недели назад')
			);

		foreach( $map as $date => $wording )
		{
			if ( $db_date >= $date ) return $wording;
		}
 	}

	protected function convertHours( $hours, $hoursInDay = 24 )
	{
		$monthes = floor($hours / (30 * $hoursInDay));
		$hours -= $monthes * $hoursInDay * 30;
		$days = floor($hours / $hoursInDay);
		$hours -= $days * $hoursInDay;
		 return array (
			 $monthes,
			 $days,
			 floor($hours),
			 round(($hours - floor($hours)) * 60,0)
		 );
	}

	 protected function convertToHoursAndMinutes( $hours )
	 {
		 return array (
			 floor($hours),
			 round(($hours - floor($hours)) * 60,0)
		 );
	 }

	function getDurationWording( $givenHours, $hoursInDay = 24 )
	{
		list( $monthes, $days, $hours, $minutes ) = $this->convertHours($givenHours, $hoursInDay);
		$result = '';
		if ( $monthes > 0 ) {
			$result .= $monthes.'мес ';
		}
		if ( $days > 0 ) {
			$result .= $days.'д ';
		}
		if ( $hours > 0 ) {
			$result .= $hours.'ч ';
		}
		if ( $minutes > 0 ) {
			$result .= $minutes.'м ';
		}
		if ( $givenHours <= 0 ) {
			$result .= 0;
		}
		return trim($result);
	}

	 function getHoursWording( $hours )
	 {
		 list( $hours, $minutes ) = $this->convertToHoursAndMinutes($hours);
		 $result = '';
		 if ( $hours > 0 ) {
			 $result .= $hours.'ч ';
		 }
		 if ( $minutes > 0 ) {
			 $result .= $minutes.'м ';
		 }
		 return trim($result);
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

	 function getDurationWording( $givenHours, $hoursInDay = 24 )
	 {
		 list( $monthes, $days, $hours, $minutes ) = $this->convertHours($givenHours, $hoursInDay);
		 $result = '';
		 if ( $monthes > 0 ) {
			 $result .= $monthes.'mo ';
		 }
		 if ( $days > 0 ) {
			 $result .= $days.'d ';
		 }
		 if ( $hours > 0 ) {
			 $result .= $hours.'h ';
		 }
		 if ( $minutes > 0 ) {
			 $result .= $minutes.'m ';
		 }
		 if ( $givenHours <= 0 ) {
			 $result .= 0;
		 }
		 return trim($result);
	 }

	 function getHoursWording( $hours )
	 {
		 list( $hours, $minutes ) = $this->convertToHoursAndMinutes($hours);
		 $result = '';
		 if ( $hours > 0 ) {
			 $result .= $hours.'h ';
		 }
		 if ( $minutes > 0 ) {
			 $result .= $minutes.'m ';
		 }
		 return trim($result);
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

 	$result = $text_data[$key];
 	
 	return $result != '' ? $result : $key;
 }
 
?>