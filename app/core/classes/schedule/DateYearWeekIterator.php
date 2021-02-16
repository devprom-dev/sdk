<?php

class DateYearWeekIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return $this->getDateFormattedShort('StartDate').' / '.$this->getDateFormattedShort('FinishDate');
    }
}