<?php

class FunctionalAreaMenuMyProjectsBuilder extends FunctionalAreaMenuFavoritesBuilder
{
    protected function createCustomReports()
    {
    	parent::createCustomReports();
    	
   		$custom = getFactory()->getObject('pm_CustomReport');
		
		$custom_it = $custom->getMyRegistry()->getAll();

	    $report_it = getFactory()->getObject('PMReport')->getExact('project-log');
		    
	    if ( $report_it->getId() != '' && !in_array('project-log', $custom_it->fieldToArray('ReportBase')) )
	    {
   		    $custom->add_parms( array (
   		            'Caption' => $report_it->getDisplayName(),
   		            'ReportBase' => $report_it->getId(),
   		            'Category' => FUNC_AREA_FAVORITES,
   		            'Url' => $report_it->get('QueryString')
   		    ));
	    }
    }
}