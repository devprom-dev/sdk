<?php

include "classes/ReportsEEPMBuilder.php";
include "classes/FunctionalAreaMenuPortfolioResManBuilder.php";
include "classes/FunctionalAreaMenuManagementResManBuilder.php";

class resmanPM extends PluginPMBase
{
	// returns builders which extend application behavior 
	public function getBuilders()
	{
		return array(
        		// reports
                new ReportsEEPMBuilder(),
				
				// menu
				new FunctionalAreaMenuPortfolioResManBuilder(),
				new FunctionalAreaMenuManagementResManBuilder()
		);
	}

    function getModules()
    {
        $base = $this->getBasePlugin();
        if ( !$base->checkLicense() ) return array();
        	
        return array (
            'resourceload' =>
                array(
                        'includes' => array( 'resman/views/ResourcePage.php' ),
                        'classname' => 'ResourcePage',
                        'title' => text('resman2'),
                        'AccessEntityReferenceName' => 'Request',
                		'area' => FUNC_AREA_MANAGEMENT
                ),
        );
    }
    
 	function interceptMethodTableGetFilters( & $table, & $filters )
 	{
 	    $base = $this->getBasePlugin();
        if ( !$base->checkLicense() ) return;
 	    
 	    if ( is_a($table, 'ReportSpentTimeTable') )
 	    {
			$filters[] = new ResourceFilterRoleWebMethod();
			$filters[] = new ResourceFilterUserWebMethod();
 	    }
 	}
}