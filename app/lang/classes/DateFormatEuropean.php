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

    function getDateFormatShort($date)
    {
        if (strftime('%Y', $date) == date('Y')) {
            return 'j - M';
        } else {
            return 'j - M Y';
        }
    }

    function getPhpDate($time)
    {
        return SystemDateTime::convertToClientTime(date('Y-m-d H:i:s', $time), 'j/m/Y');
    }

    function getPhpDateTime($time)
    {
        return date('j/m/Y H:i:s', $time);
    }

    function getDateJSFormat()
    {
        return 'dd/MM/yyyy';
    }

    function getDbDate($text)
    {
        list($day, $month, $year) = explode('/', $text);

        if ($year < 1 || $month < 1 || $day < 1) return "";

        if (!checkdate($month, $day, $year)) return '';

        return $year . "-" . $month . "-" . $day;
    }

    function getDaysWording($days)
    {
        if ($days == 1) {
            return 'day';
        } else {
            return 'days';
        }
    }
}