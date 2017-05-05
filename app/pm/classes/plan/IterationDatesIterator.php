<?php

class IterationDatesIterator extends OrderedIterator
{
    function getDisplayName()
    {
        $title = parent::getDisplayName();
        if ( parent::get('StartDate') == '' || parent::get('FinishDate') == '' ) return $title;
        return sprintf('%s &nbsp; [%s : %s]', $title,
            getSession()->getLanguage()->getDateFormattedShort(parent::get('StartDate')),
            getSession()->getLanguage()->getDateFormattedShort(parent::get('FinishDate'))
        );
    }
}