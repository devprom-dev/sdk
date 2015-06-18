<?php

include_once SERVER_ROOT_PATH."core/methods/FilterWebMethod.php";
include_once SERVER_ROOT_PATH."core/methods/FilterAutoCompleteWebMethod.php";

///////////////////////////////////////////////////////////////////////////////////////
 class ViewVersionWebMethod extends FilterAutoCompleteWebMethod
 {
 	function getCaption()
 	{
 		return translate('Версия продукта');
 	}
 	
 	function ViewVersionWebMethod()
 	{
 		global $model_factory;
 		
 		parent::FilterAutoCompleteWebMethod(
 			$model_factory->getObject('Version'), $this->getCaption() );
 	}
 	
 	function getValueParm()
 	{
 		return 'version';
 	}
 	
 	function getStyle()
 	{
 		return 'width:160px;';
 	}
 	
	function hasAccess()
	{
		return getSession()->getProjectIt()->getMethodologyIt()->HasReleases();
	}
 }
 
 ///////////////////////////////////////////////////////////////////////////////////////
 class ReleaseWebMethod extends WebMethod
 {
 }
