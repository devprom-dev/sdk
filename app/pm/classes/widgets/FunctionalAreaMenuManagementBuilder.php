<?php

include_once "FunctionalAreaMenuCommonBuilder.php";

class FunctionalAreaMenuManagementBuilder extends FunctionalAreaMenuCommonBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    global $model_factory;
 	    
 	    $menus = parent::build($set);
		unset($menus['tasks']);
 	    
		$module = $model_factory->getObject('Module');
		$report = $model_factory->getObject('PMReport');
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$menus['quick']['items'][] = $module->getExact('project-knowledgebase')->buildMenuItem();
		$menus['quick']['items'][] = $report->getExact('project-log')->buildMenuItem();
		$menus['quick']['items'][] = $module->getExact('project-blog')->buildMenuItem();
	 	$menus['quick']['items'][] = $module->getExact('project-question')->buildMenuItem();
        
 		// plan items
		$items = array();
		$items[] = $report->getExact('projectplan')->buildMenuItem();
		$items[] = $module->getExact('project-plan-milestone')->buildMenuItem();
		$items[] = $report->getExact('tasksplanningboard')->buildMenuItem();
		$items[] = $report->getExact('currenttasks')->buildMenuItem();
		$items[] = $module->getExact('tasks-board')->buildMenuItem();
		$items[] = $report->getExact('tasks-trace')->buildMenuItem();
		
 		$menus = array_merge( array_slice($menus, 0, 1), array( 'plan' => array ( 
			'name' => translate('План'), 
			'items' => $items,
 		    'uid' => 'plan')), array_slice($menus, 1) 
 		);

		// product tab
		$items = array();

		$items['features-list'] = $module->getExact('features-list')->buildMenuItem();
		$items['productbacklog'] = $report->getExact('productbacklog')->buildMenuItem();

		$module_it = $module->getExact('issues-board');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
			$items['issues-board'] = $module_it->buildMenuItem();
		}
		$items['issues-trace'] = $report->getExact('issues-trace')->buildMenuItem();

		$menus['features'] = array(
			'name' => translate('Продукт'),
			'items' => $items,
			'uid' => 'features'
		);

 		// reports items
		$items = array();

		$items[] = $report->getExact('features-chart')->buildMenuItem();
		$items[] = $report->getExact('activitiesreport')->buildMenuItem();
		$items['all'] = $module->getExact('project-reports')->buildMenuItem(FUNC_AREA_MANAGEMENT);
		
		$menus['reports'] = array (
            'name' => translate('Графики и отчеты'),
            'uid' => 'reports',
            'items' => $items
 	    );
 	    
		$set->setAreaMenus( FUNC_AREA_MANAGEMENT, $menus );
    }
}