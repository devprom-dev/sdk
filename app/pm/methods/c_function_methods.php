<?php

include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";

 ///////////////////////////////////////////////////////////////////////////////////////
 class FuncionWebMethod extends WebMethod
 {
 	function execute_request()
 	{
 		global $_REQUEST;
	 	if($_REQUEST['Function'] != '') {
	 		$this->execute($_REQUEST['Function']);
	 	}
 	}
 }

 ///////////////////////////////////////////////////////////////////////////////////////
 class FunctionFilterWebMethod extends FilterWebMethod
 {
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class FunctionFilterViewWebMethod extends FunctionFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('���');
 	}
 	
 	function getValues()
 	{
  		return array (
 			'list' => translate('������'), 
 			'chart' => translate('������ ��������'),
 			'trace' => translate('�����������')
 			);
	}

	function getStyle()
	{
		return 'width:130px;';
	}

 	function getValueParm()
 	{
 		return 'view';
 	}
 
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return 'list'; 
 		}
 		
 		return $value;
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 }
 
 //////////////////////////////////////////////////////////////////////////////////////
 class FunctionFilterStateWebMethod extends FunctionFilterWebMethod
 {
 	function getCaption()
 	{
 		return translate('���������');
 	}

 	function getValues()
 	{
  		return array (
 			'all' => translate('���'), 
 			'open' => translate('�� �����������'),
 			'closed'  => translate('�����������')
 			);
	}

	function getStyle()
	{
		return 'width:125px;';
	}

 	function getValueParm()
 	{
 		return 'state';
 	}
 
 	function getValue()
 	{
 		$value = parent::getValue();
 		
 		if ( $value == '' )
 		{
 			return 'all'; 
 		}
 		
 		return $value;
 	}
 	
 	function getType()
 	{
 		return 'singlevalue';
 	}
 }
  
 //////////////////////////////////////////////////////////////////////////////////////
 class FunctionFilterStageWebMethod extends FilterAutoCompleteWebMethod
 {
 	function getCaption()
 	{
 		return translate('������ �������');
 	}

 	function FunctionFilterStageWebMethod()
 	{
 		global $model_factory;
 		
		$this->object = $model_factory->getObject('Stage'); 
 		parent::FilterAutoCompleteWebMethod( $this->object, $this->getCaption() );
 	}
 	
	function getStyle()
	{
		return 'width:140px;';
	}

 	function getValueParm()
 	{
 		return 'stage';
 	}
 
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasReleases();
	}
 }
 