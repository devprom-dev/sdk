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

        $menus['quick']['items'][] = $module->getExact('dashboard')->buildMenuItem();
		$menus['quick']['items'][] = $module->getExact('project-knowledgebase')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('projectplan')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('currenttasks')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('tasksplanningboard')->buildMenuItem();
		$menus['quick']['items'][] = $module->getExact('project-log')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('tasks-board')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('tasks-trace')->buildMenuItem();
        $menus['quick']['items']['features-list'] = $module->getExact('features-list')->buildMenuItem();
        $menus['quick']['items']['productbacklog'] = $report->getExact('productbacklog')->buildMenuItem();

		$module_it = $module->getExact('issues-board');
		if ( getFactory()->getAccessPolicy()->can_read($module_it) ) {
            $menus['quick']['items']['issues-board'] = $module_it->buildMenuItem();
		}
        $menus['quick']['items']['issues-trace'] = $report->getExact('issues-trace')->buildMenuItem();

 		// reports items
		$items = array();

        $items[] = $module->getExact('delivery')->buildMenuItem();
		$items[] = $report->getExact('features-chart')->buildMenuItem();
		$items[] = $report->getExact('activitiesreport')->buildMenuItem();

		$menus['reports'] = array (
            'name' => text(2230),
            'uid' => 'reports',
            'items' => $items
 	    );
 	    
		$set->setAreaMenus( FUNC_AREA_MANAGEMENT, $menus );
    }

    protected function getAreaUid() {
        return FUNC_AREA_MANAGEMENT;
    }
}