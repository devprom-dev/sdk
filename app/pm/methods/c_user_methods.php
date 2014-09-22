<?php
 
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class FilterUserAuthorWebMethod extends FilterAutoCompleteWebMethod
 {
 	function FilterUserAuthorWebMethod()
 	{
 		global $model_factory;
 		
 		parent::FilterAutoCompleteWebMethod(
 			$model_factory->getObject('cms_User'), translate('Автор') 
 			);
 	}
 	
 	function getValueParm()
 	{
 		return 'author';
 	}
 	
 	function getStyle()
 	{
 		return 'width:140px;';
 	}
 }
 
