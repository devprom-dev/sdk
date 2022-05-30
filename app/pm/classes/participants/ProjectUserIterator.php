<?php

class ProjectUserIterator extends UserIterator
{
    function getDisplayNameExt( $prefix = '' )
    {
        if ( defined('UI_EXTENSION') && !UI_EXTENSION ) return parent::getDisplayNameExt();

        $title = parent::getDisplayNameExt();

        if ( $this->get('FreeWorkingDate') > date('Y-m-d') ) {
            return $title . sprintf(text(2495), getSession()->getLanguage()->getDateFormattedShort($this->get('FreeWorkingDate')));
        }

        return $title;
    }
}