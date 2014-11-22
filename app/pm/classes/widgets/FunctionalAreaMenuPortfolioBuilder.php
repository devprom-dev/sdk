<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuPortfolioBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    $menus = parent::build($set);
 	    
 	    $methodology_it = getSession()->getProjectIt()->getMethodologyIt();
 	    
		$module = getFactory()->getObject('Module');
		$report = getFactory()->getObject('PMReport');
		
		$items = array();
		
		$items[] = $report->getExact('features-chart')->buildMenuItem();
        
		$module_it = $module->getExact('issues-board');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$items[] = $report->getExact('issuesboardcrossproject')->buildMenuItem();
		}

        $task_chart_it = $module->getExact('tasks-board');
        if ( $methodology_it->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$items[] = $report->getExact('tasksboardcrossproject')->buildMenuItem();
        }
		
		$items[] = $report->getExact('activitiesreport')->buildMenuItem();
		$items[] = $report->getExact('discussions')->buildMenuItem();
		$items[] = $report->getExact('project-blog')->buildMenuItem();
		
		$menus['quick']['items'] = array_merge($items, $menus['quick']['items']);
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
    }
}