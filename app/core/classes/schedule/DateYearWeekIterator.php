<?php

class DateYearWeekIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return $this->getDateFormatShort('StartDate').' / '.$this->getDateFormatShort('FinishDate');
    }
}