<?php

include_once "FunctionalAreaMenuMyProjectsBuilder.php";

class FunctionalAreaMenuPortfolioBuilder extends FunctionalAreaMenuMyProjectsBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    $menus = parent::build($set);
 	    
		$module = getFactory()->getObject('Module');
		$report = getFactory()->getObject('PMReport');
		
		$items = array();
		
		$module_it = $module->getExact('issues-board');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) )
		{
			$items[] = $report->getExact('issuesboardcrossproject')->buildMenuItem();
		}

        $task_chart_it = $module->getExact('tasks-board');
        if ( getSession()->getProjectIt()->getMethodologyIt()->HasTasks() && getFactory()->getAccessPolicy()->can_read($task_chart_it) )
        {
			$items[] = $report->getExact('tasksboardcrossproject')->buildMenuItem();
        }
		
		$item = $report->getExact('project-blog')->buildMenuItem();
		$item['name'] = text(2000);
		$items[] = $item;
		
		$menus['quick']['items'] = array_merge($menus['quick']['items'], $items);
		
		$this->buildResourcesFolder($menus);
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
    }

	protected function createCustomReports()
    {
    	if ( !class_exists('PortfolioMyProjectsBuilder', false) ) return parent::createCustomReports();
    	// skip creating custom reports like My Tasks, etc.
    }

    protected function buildResourcesFolder( &$menus )
    {
    	$menus['resources'] = array (
 	        'name' => translate('Ресурсы'),
            'uid' => 'resources',
            'items' => array(
            				getFactory()->getObject('PMReport')->getExact('activitiesreport')->buildMenuItem('group=SystemUser')
    				   )
 	    );
    }
}