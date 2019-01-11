<?php
 
include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterDateWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class TaskWebMethod extends WebMethod
 {
 	function TaskWebMethod()
 	{
 		parent::WebMethod();
 	}
 }

 //////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskWebMethod extends FilterWebMethod
 {
 	var $ids;

 	function ViewTaskWebMethod ( $parms_array = array() )
 	{
 		$this->ids = $parms_array;
 		
 		parent::FilterWebMethod();
 	}

 	function execute ( $setting, $value )
 	{
 	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ViewTaskDateWebMethod extends FilterDateWebMethod
 {
 	function getStyle()
 	{
 		return 'width:70px;height:18px;';
 	}
 }

