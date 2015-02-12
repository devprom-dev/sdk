<?php

class FunctionalAreaMenuMyProjectsBuilder extends FunctionalAreaMenuFavoritesBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);

    	if ( getFactory()->getObject('Project')->getRegistry()->Count() < 1 && !defined('SKIP_WELCOME_PAGE') )
    	{
			$menus['quick']['items'] = array_merge(
					array (
							array (
									'uid' => 'welcome',
									'url' => '/projects/welcome'
							)
					),
					$menus['quick']['items']
			);
    	} 

		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
		
		return $menus;
    }
	
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
   		            'Url' => $report_it->get('QueryString'),
   		    		'OrderNum' => 20
   		    ));
	    }

        $report_it = getFactory()->getObject('PMReport')->getExact('discussions');
	    if ( $report_it->getId() != '' && !in_array('discussions', $custom_it->fieldToArray('ReportBase')) )
	    {
   		    $custom->add_parms( array (
   		            'Caption' => $report_it->getDisplayName(),
   		            'ReportBase' => $report_it->getId(),
   		            'Category' => FUNC_AREA_FAVORITES,
   		            'Url' => $report_it->get('QueryString'),
   		    		'OrderNum' => 30
   		    ));
	    }
    }
}