<?php

class IterationDatesIterator extends OrderedIterator
{
    function getDisplayName()
    {
        $title = $this->get('FullCaption') != '' ? $this->get('FullCaption') : parent::getDisplayName();

        if ( getSession()->getProjectIt()->IsPortfolio() || getSession()->getProjectIt()->IsProgram() ) {
            $title = $this->getRef('Project')->getDisplayName() . '.'.$title;
        }
        if ( parent::get('StartDate') == '' || parent::get('FinishDate') == '' ) return $title;

        $startDate = parent::getDateFormattedShort('StartDate');
        $finishDate = parent::getDateFormattedShort('FinishDate');

        $finishDateObject = new DateTime(parent::get('FinishDate'));
        $intervalFinish = $finishDateObject->diff(new DateTime(parent::get('EstimatedFinishDate')));

        $startDateObject = new DateTime(parent::get('StartDate'));
        $intervalStart = $startDateObject->diff(new DateTime(parent::get('EstimatedStartDate')));

        return sprintf('%s [%s : %s]', $title,
            ($intervalStart->invert ? -1 : 1) * $intervalStart->days > 0
                ? '<span class="label label-important">'.$startDate.'</span>'
                : $startDate,
            ($intervalFinish->invert ? -1 : 1) * $intervalFinish->days > 0
                ? '<span class="label label-important">'.$finishDate.'</span>'
                : $finishDate
        );
    }
}