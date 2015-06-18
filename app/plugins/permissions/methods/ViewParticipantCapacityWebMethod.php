<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

class ViewParticipantCapacityWebMethod extends FilterWebMethod
{
 	function getCaption()
 	{
 		return translate('Загрузка');
 	}
 	
 	function getValues()
 	{
  		$values = array (
 			'all' => translate('Все'),
 			'none' => text(1250),
 			'use' => text(1251)
  		);

 		return $values;
	}
	
	function getStyle()
	{
		return 'width:120px;';
	}
	
	function getValueParm()
	{
		return 'workload';
	}
	
	function getValue()
	{
		$value = parent::getValue();
		
		if ( $value == '' )
		{
			$value = 'all';
		}
		
		return $value;
	}

  	function getType()
 	{
 		return 'singlevalue';
 	}
}