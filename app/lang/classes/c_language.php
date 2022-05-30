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
 	
 	function Initialize()
 	{
 	    global $text_data;
        if ( $this->getLanguage() == '' ) return;

        $resource = getFactory()->getObject('cms_Resource');
        $resource->setLanguageUid( $this->getLanguage() );

 	    $cache_path = $this->getCacheFilePath($resource);
 	    if ( !file_exists($cache_path) ) {
            $this->buildCache($resource, $cache_path);
 	    }

		$text_data = include($cache_path);
		return;
 	}
 	
 	protected function buildCache($resource, $cache_path)
 	{
		$lock = new CacheLock();
        $lock->Lock();

 		$records = array();
 	    $resource_it = $resource->getAll();
 	    $data = $resource_it->getRowset();
 	    foreach( $data as $key => $value ) {
 	    	$records[] = "'".$value['ResourceKey']."' => '".preg_replace("/'/", "\\'", $value['ResourceValue'])."'"; 
 	    }
 	    @mkdir(dirname($cache_path), 0777, true);
 	    file_put_contents($cache_path, '<?php return array('.join(',',$records).');');

        $lock->Release();
 	}
 	
 	protected function getCacheFilePath( $resource ) {
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
 		return strftime($this->dateformat->getDateFormat(), strtotime($value));
 	}
 	
 	function getDateFormattedShort( $value )
 	{
 		if ( $value == '' ) return $value;
        $time = new DateTime($value, new DateTimeZone("UTC"));
        return $time->format($this->dateformat->getDateFormatShort(strtotime($value)));
 	}
 	
 	function getDateUserFormatted( $value, $format )
 	{
 		if ( $value == '' ) return $value;
 		return strftime($format, strtotime($value));
 	}

 	function getDateTimeFormatted( $value )
 	{
 		if ( $value == '' ) return $value;
 		
 		return strftime($this->dateformat->getDateFormat().' %H:%M:%S',
            strtotime(SystemDateTime::convertToClientTime($value))
        );
 	}
 	
 	function getTimeFormatted( $value )
 	{
 		return strftime('%H:%M', strtotime(SystemDateTime::convertToClientTime($value)));
 	}
 	
 	function getPhpDate( $time )
 	{
 		return $this->dateformat->getPhpDate($time); 
 	}

     function getPhpDateTime( $time )
     {
         return $this->dateformat->getPhpDateTime($time);
     }

     function getExcelDateFormat()
     {
         return $this->dateformat->getExcelDateFormat();
     }

     function getExcelDateTimeFormat()
     {
         return $this->dateformat->getExcelDateTimeFormat();
     }

 	function getDbDate( $text )
 	{
 	    if ( $text == '' ) return $text;
        $dbdate = $this->dateformat->getDbDate($text);
        if ( $dbdate == '' ) {
            $time = strtotime($text);
            if ( $time === false ) return '';
            return strftime("%Y-%m-%d", $time);
        }
        return $dbdate;
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
        if ( abs($monthes) > 0 ) {
            $result .= abs($monthes).'мес ';
        }
        if ( abs($days) > 0 ) {
            $result .= abs($days).'д ';
        }
		if ( abs($hours) > 0 ) {
			$result .= abs($hours).'ч ';
		}
		if ( abs($minutes) > 0 ) {
			$result .= abs($minutes).'м ';
		}
		if ( $givenHours <= 0 ) {
			$result .= 0;
		}
		return ($givenHours < 0 ? '-' : '') .  trim($result);
	}

	 function getHoursWording( $hours )
	 {
		 list( $hours, $minutes ) = $this->convertToHoursAndMinutes($hours);
		 $result = '';
		 if ( abs($hours) > 0 ) {
			 $result .= abs($hours).'ч ';
		 }
		 if ( abs($minutes) > 0 ) {
			 $result .= abs($minutes).'м ';
		 }
		 return ($hours < 0 ? '-' : '') . trim($result);
	 }

     public function formatFloatValue($value, $attributeGroups = array())
     {
         if ( $value == '' ) return $value;

         if ( in_array('hours', $attributeGroups) ) {
             return $this->getHoursWording($value);
         }
         elseif ( in_array('astronomic-time', $attributeGroups) ) {
             return $this->getDurationWording($value);
         }
         elseif ( in_array('working-time', $attributeGroups) ) {
             return $this->getDurationWording($value, 8);
         }
         else {
             return number_format(floatval($value),
                 \EnvironmentSettings::getFloatPrecision(), ',', ' ');
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

	 function getDurationWording( $givenHours, $hoursInDay = 24 )
	 {
		 list( $monthes, $days, $hours, $minutes ) = $this->convertHours($givenHours, $hoursInDay);
		 $result = '';
		 if ( abs($monthes) > 0 ) {
			 $result .= abs($monthes).'mo ';
		 }
		 if ( abs($days) > 0 ) {
			 $result .= abs($days).'d ';
		 }
		 if ( abs($hours) > 0 ) {
			 $result .= abs($hours).'h ';
		 }
		 if ( abs($minutes) > 0 ) {
			 $result .= abs($minutes).'m ';
		 }
		 if ( $givenHours <= 0 ) {
			 $result .= 0;
		 }
		 return ($givenHours < 0 ? '-' : '') . trim($result);
	 }

	 function getHoursWording( $hours )
	 {
		 list( $hours, $minutes ) = $this->convertToHoursAndMinutes($hours);
		 $result = '';
		 if ( abs($hours) > 0 ) {
			 $result .= abs($hours).'h ';
		 }
		 if ( abs($minutes) > 0 ) {
			 $result .= abs($minutes).'m ';
		 }
		 return ($hours < 0 ? '-' : '') . trim($result);
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