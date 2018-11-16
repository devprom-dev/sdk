<?php

class DateFormatBase
{
	function getDisplayName()
	{
	}
	
 	function getDateFormat() 
 	{
 	}

 	function getDateFormatShort( $date )
 	{
 	}
 	
 	function getDbDate( $text )
 	{
 	}
 	
 	function getPhpDate( $time )
 	{
 	}

    function getPhpDateTime( $time )
    {
    }

 	function getDatepickerFormat()
 	{
 	}
 	
 	function getDatepickerLanguage()
 	{
 	}
 	
 	function getDateJSFormat()
 	{
 	}
 	
 	function getDaysWording( $days )
 	{
 	}

 	function getExcelDateFormat() {
	    return 'dd.mm.yyyy';
    }

    function getExcelDateTimeFormat() {
        return 'dd.mm.yyyy h:mm:ss';
    }
}