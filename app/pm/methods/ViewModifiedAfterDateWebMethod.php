<?php

class ViewModifiedAfterDateWebMethod extends FilterDateIntervalWebMethod
{
    function __construct() {
        parent::__construct();
        $this->setCaption(translate('Изменено'));
    }

    function getValueParm() {
        return 'modifiedafter';
    }
}
