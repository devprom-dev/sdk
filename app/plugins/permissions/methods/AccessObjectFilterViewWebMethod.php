<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class AccessObjectFilterViewWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return text(757);
 	}

 	function getValues()
 	{
  		return array (
 			'all' => translate('Все'),
 			'module' => translate('Модули'),
 			'report' => translate('Отчеты'),
 			'entity' => translate('Сущности'),
 			'attribute' => translate('Атрибуты'),
  			'object' => translate('Объекты')
 			);
	}
	
	function getStyle()
	{
		return 'width:200px;';
	}
	
	function getValueParm()
	{
		return 'object';
	}
	
	function getType()
	{
		return 'singlevalue';
	}
}