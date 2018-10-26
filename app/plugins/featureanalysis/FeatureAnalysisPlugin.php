<?php

include "classes/c_featureanalysis.php";
include "classes/c_competitor.php";
include "FeatureAnalysisPMPlugin.php";

class FeatureAnalysisPlugin extends PluginBase
{
 	function getNamespace()
 	{
 		return 'featureanalysis';
 	}
 
  	function getFileName()
 	{
 		return 'featureanalysis.php';
 	}
 	
 	function getCaption()
 	{
 		return text('featureanalysis1');
 	}
 	
 	function getSectionPlugins()
 	{
 		return array( new FeatureAnalysisPMPlugin );
 	}

  	function getIndex()
 	{
 		return parent::getIndex() + 150;
 	}
 	
 	function getClasses()
 	{
 		return array (
 			'pm_competitor' => array( 'Competitor', 'c_competitor.php' ),
 			'pm_featureanalysis' => array( 'FeatureAnalysis', 'c_featureanalysis.php' )
 		);
 	}
 	
  	function buildAreaMenu( $menus )
 	{
 	    global $project_it;
 	    
 	    $base = '/pm/'.$project_it->get('CodeName').'/';
 	    		
 	    // features tab 
 	    
 	 	foreach ( $menus as $key => $tab )
		{
		    if ( $tab['uid'] == 'features' )
		    {
		        $menu[$key]['items'][] = array( 
		            'name' => text('featureanalysis2'),
		            'url' => $base.'module/featureanalysis/features'
		        );
        		
        		break;
		    }
		}
 	    
 	    return $menus;
 	}
}
  