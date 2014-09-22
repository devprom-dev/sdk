<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewParticipantWebMethod extends FilterWebMethod
 {
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewSpentTimeWebMethod extends ViewParticipantWebMethod
 {
 	function getCaption()
 	{
 		return translate('���');
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'participants' => translate('���������'),
 			'projects' => translate('�������'),
  		    'issues' => translate('���������'),
 			'tasks' => translate('������')
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

 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewParticipantCapacityWebMethod extends ViewParticipantWebMethod
 {
 	function getCaption()
 	{
 		return translate('��������');
 	}
 	
 	function getValues()
 	{
 		global $model_factory;
 		
  		$values = array (
 			'all' => translate('���'),
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
 
?>