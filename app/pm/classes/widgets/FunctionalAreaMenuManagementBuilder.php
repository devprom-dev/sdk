<?php

include_once "FunctionalAreaMenuCommonBuilder.php";

class FunctionalAreaMenuManagementBuilder extends FunctionalAreaMenuCommonBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    global $model_factory;
 	    
 	    $menus = parent::build($set);
 	    
 	    $project_it = getSession()->getProjectIt();
 	    
 	    $part_it = getSession()->getParticipantIt();
 	    
		$base = getSession()->getApplicationUrl();
 	    
		$module = $model_factory->getObject('Module');
		
		$report = $model_factory->getObject('PMReport');
		
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		
		$menus['quick']['items'][] = $module->getExact('project-knowledgebase')->buildMenuItem();
		
		// main tab
		
		$tab = array();
		
		$items[] = $report->getExact('project-log')->buildMenuItem();

		$items[] = $module->getExact('project-blog')->buildMenuItem();
		
	 	$items[] = $module->getExact('project-question')->buildMenuItem();
        
 		$tab = array ( 
			'name' => $project_it->IsProgram() ? translate('Программа') : translate('Проект'), 
			'items' => $items,
 		    'uid' => 'project'
 		);
 		
 		$menus = array_merge(array_slice($menus, 0, 1), array('project' => $tab), array_slice($menus, 1));
		
		// releases (or issues) tab
		$items = array();
 		
		if ( $methodology_it->HasReleases() )
		{
   		    $items[] = $report->getExact('releaseplanningboard')->buildMenuItem();
		    
		    $items[] = $report->getExact('issues-trace')->buildMenuItem();
    		
     		$menus[] = array ( 
    			'name' => translate('Релизы'), 
    			'items' => $items,
     		    'uid' => 'releases'
     		);
		}
 		
 		// iterations tab
 		
		unset($menus['tasks']);
 		
		if ( $methodology_it->HasPlanning() )
		{
		    $items = array();
		    
       		$items[] = $report->getExact('iterationplanningboard')->buildMenuItem();
    		
		    $items[] = $report->getExact('tasks-trace')->buildMenuItem();
    		
     		$menus[] = array ( 
    			'name' => translate('Итерации'), 
    			'items' => $items,
     		    'uid' => 'iterations'
     		);
		}
		
 		// plan items

		$items = array();
		
		$items[] = $report->getExact('projectplan')->buildMenuItem();
		
		$items[] = $module->getExact('project-plan-milestone')->buildMenuItem();
		
		$items[] = $report->getExact('currenttasks')->buildMenuItem();
		
		$items[] = $module->getExact('tasks-board')->buildMenuItem();
		
 		$menus = array_merge( array_slice($menus, 0, 2), array( 'plan' => array ( 
			'name' => translate('План'), 
			'items' => $items,
 		    'uid' => 'plan')), array_slice($menus, 2) 
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