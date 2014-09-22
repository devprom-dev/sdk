<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuPortfolioBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    global $model_factory;
 	    
 	    $menus = parent::build($set);
 	    
        $custom = $model_factory->getObject('pm_CustomReport');
		
        $custom_it = $custom->getMyRegistry()->getAll();
		
		if ( $custom_it->count() < 1 )
		{
		    // append default reports
		    $report = $model_factory->getObject('PMReport');
		    
		    $report_it = $report->getExact('project-blog');
		    
		    if ( $report_it->getId() != '' )
		    {
    		    $custom->add_parms( array (
    		            'Caption' => translate('Блоги'),
    		            'ReportBase' => $report_it->getId(),
    		            'Category' => FUNC_AREA_FAVORITES,
    		            'Url' => $report_it->get('QueryString')
    		    ));
		    }

			$report_it = $report->getExact('productbacklog');
		    
		    if ( getFactory()->getAccessPolicy()->can_read($report_it) != '' )
		    {
    		    $custom->add_parms( array (
    		    		'Caption' => $report_it->getDisplayName(),
    		            'ReportBase' => $report_it->getId(),
    		            'Category' => FUNC_AREA_FAVORITES,
    		            'Url' => $report_it->get('QueryString')
    		    ));
		    }
		}
 	    
		$module = $model_factory->getObject('Module');
		
		$report = $model_factory->getObject('PMReport');
						
		$custom = $model_factory->getObject('pm_CustomReport');
		
		$custom_it = $custom->getAll();

		while ( !$custom_it->end() )
		{
			if ( getFactory()->getAccessPolicy()->can_read($custom_it) && $custom_it->get('Category') == FUNC_AREA_FAVORITES )
			{
			    $it = $report->getExact($custom_it->getId());
			    
			    $uid = $it->getId();
			    
				$items[$uid] = $it->buildMenuItem();
				
				$items[$uid]['uid'] = $uid;
			}
			
			$custom_it->moveNext();
		}
		
		$menus['quick']['items'] = array_merge($items, $menus['quick']['items']);  
		
 		// reports items
 		
		$items = array();
		
		$items[] = $report->getExact('project-log')->buildMenuItem();
		
		$items[] = $report->getExact('features-chart')->buildMenuItem();

		$items[] = $report->getExact('activitiesreport')->buildMenuItem();
		
		$menus['reports'] = array (
            'name' => translate('Отчеты'),
            'uid' => 'reports',
            'items' => $items
 	    );
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
    }
}