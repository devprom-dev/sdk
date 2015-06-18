<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuCommonBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);
    	
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$module = getFactory()->getObject('Module');
		
		$report = getFactory()->getObject('PMReport');
		
		$items = array();

		$items['features-list'] = $module->getExact('features-list')->buildMenuItem();
        
		$items['myissues'] = $report->getExact('myissues')->buildMenuItem();
 			    
		$items['productbacklog'] = $report->getExact('productbacklog')->buildMenuItem();

		$module_it = $module->getExact('issues-board');
		    
 		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
 		{
        	$items['issues-board'] = $module_it->buildMenuItem();
 		}

       	$items['featurestrace'] = $report->getExact('featurestrace')->buildMenuItem();
		
		$menus['features'] = array( 
		   'name' => $methodology_it->HasFeatures() ? translate('Продукт') : translate('Пожелания'), 
		   'items' => $items,
		   'uid' => 'features' 
		);

		$report_it = $report->getExact('mytasks');
		
    	if ( $report_it->getId() != '' )
		{
			$items = array();
			
      	    $items['mytasks'] = $report_it->buildMenuItem();
			    
		 	$items['tasks-board'] = $module->getExact('tasks-board')->buildMenuItem('?');
			
    		$menus['tasks'] = array( 
    		   'name' => translate('Задачи'), 
    		   'items' => $items,
    		   'uid' => 'tasks' 
    		);
		}
		
		return $menus;
    }
}