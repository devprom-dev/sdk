<?php

class ViewModifiedBeforeDateWebMethod extends FilterDateIntervalWebMethod
{
    function getCaption() {
        return translate('Изменено');
    }

    function getValueParm() {
        return 'modifiedbefore';
    }
}
