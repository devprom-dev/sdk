<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewRevisionViewWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return translate('Вид');
 	}
 	
	function getStyle()
	{
		return 'width:120px;';
	}
	
	function getValueParm()
	{
		return 'view';
	}
 
 	function getType()
 	{
 		return 'singlevalue';
 	}
}
