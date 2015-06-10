<?php

include_once "FilterDateWebMethod.php";

class ViewSubmmitedBeforeDateWebMethod extends FilterDateWebMethod
{
 	function getCaption()
 	{
 		return translate('Добавлено до');
 	}

	function getStyle()
	{
		return 'width:100px;';
	}

	function getValueParm()
	{
		return 'submittedbefore';
	}
}
