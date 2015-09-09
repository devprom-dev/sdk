<?php

class DateYearWeekIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return $this->getDateFormat('StartDate').' / '.$this->getDateFormat('FinishDate');
    }
}