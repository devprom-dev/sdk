<?php

class IterationDatesIterator extends OrderedIterator
{
    function getDisplayName()
    {
        $title = parent::getDisplayName();
        if ( getSession()->getProjectIt()->IsPortfolio() || getSession()->getProjectIt()->IsProgram() ) {
            $title = $this->getRef('Project')->getDisplayName() . '.'.$title;
        }
        if ( parent::get('StartDate') == '' || parent::get('FinishDate') == '' ) return $title;

        $finishDate = getSession()->getLanguage()->getDateFormattedShort(parent::get('FinishDate'));
        $finishDateObject = new DateTime(parent::get('FinishDate'));
        $interval = $finishDateObject->diff(new DateTime());

        return sprintf('%s &nbsp; [%s : %s]', $title,
            getSession()->getLanguage()->getDateFormattedShort(parent::get('StartDate')),
            ($interval->invert ? -1 : 1) * $interval->days > 0
                ? '<span class="label label-important">'.$finishDate.'</span>'
                : $finishDate
        );
    }
}