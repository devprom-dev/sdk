<?php

include_once SERVER_ROOT_PATH."pm/classes/product/FeatureModelExtendedBuilder.php";

include "FunctionForm.php";
include "FunctionTable.php";
include "PageSettingFeatureBuilder.php";

class FunctionsPage extends PMPage
{
 	function FunctionsPage()
 	{
 	    getSession()->addBuilder( new PageSettingFeatureBuilder() );
 	    
 		parent::PMPage();
 	}
 	
 	function getObject()
 	{
		global $model_factory;

		getSession()->addBuilder( new FeatureModelExtendedBuilder() );
		
 		return $model_factory->getObject('Feature');
 	}
 	
 	function getTable() 
 	{
		return new FunctionTable( $this->getObject() );
 	}
 	
 	function getForm() 
 	{
		return new FunctionForm( $this->getObject() );
 	}
}