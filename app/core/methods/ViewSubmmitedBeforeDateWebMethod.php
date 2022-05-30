<?php

class ViewSubmmitedBeforeDateWebMethod extends FilterDateIntervalWebMethod
{
    function getCaption() {
        return translate('Добавлено');
    }

	function getValueParm() {
		return 'submittedbefore';
	}
}
