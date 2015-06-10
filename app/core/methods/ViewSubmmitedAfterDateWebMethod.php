<?php

include_once "FilterDateWebMethod.php";

class ViewSubmmitedAfterDateWebMethod extends FilterDateWebMethod
{
 	function getCaption()
 	{
 		return translate('Добавлено после');
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'submittedon';
	}
}
