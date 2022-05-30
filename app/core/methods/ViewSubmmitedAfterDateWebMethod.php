<?php

class ViewSubmmitedAfterDateWebMethod extends FilterDateIntervalWebMethod
{
 	function getCaption() {
 		return translate('Добавлено');
 	}

	function getValueParm() {
		return 'submittedon';
	}
}
