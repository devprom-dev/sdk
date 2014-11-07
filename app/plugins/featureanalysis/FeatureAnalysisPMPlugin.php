<?php

include "classes/ChangeLogEntitiesFeatureAnalysisBuilder.php";
include "classes/CustomizableObjectBuilderFeatureAnalysis.php";

class FeatureAnalysisPMPlugin extends PluginPMBase
{
 	function getModules()
 	{
 		global $model_factory;
 		
 		$modules = array (
 			'features' => 
 				array(
 					'includes' => array( 'featureanalysis/views/c_product_view.php' ),
 					'classname' => 'FeatureAnalysisPage' 
 					)
 			);

 		return $modules;
 	}
 	
 	function getBuilders()
 	{
 	    return array( 
 	            new ChangeLogEntitiesFeatureAnalysisBuilder(),
 	            new CustomizableObjectBuilderFeatureAnalysis(getSession())
 	    );
 	}
}
