<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewSpentTimeWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return translate('Вид');
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'participants' => translate('Участники'),
 			'projects' => translate('Проекты'),
  		    'issues' => translate('Пожелания'),
 			'tasks' => translate('Задачи')
 			);

 		return $values;
	}
	
	function getValue()
	{
	    $value = parent::getValue();

	    if ( $value == '' ) return 'issues';
	    
	    return $value;
	}
	
	function getStyle()
	{
		return 'width:145px;';
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