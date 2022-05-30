<?php
include_once "FunctionalAreaMenuCommonBuilder.php";

class FunctionalAreaMenuManagementBuilder extends FunctionalAreaMenuCommonBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    $menus = parent::build($set);
		unset($menus['tasks']);
 	    
		$module = getFactory()->getObject('Module');
		$report = getFactory()->getObject('PMReport');

        $menus['quick']['items'][] = $module->getExact('dashboard')->buildMenuItem();
		$menus['quick']['items'][] = $module->getExact('project-knowledgebase')->buildMenuItem();
        $menus['quick']['items']['features-list'] = $module->getExact('features-list')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('projectplan')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('project-plan-hierarchy')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('tasksbyassignee')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('mytasks')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('tasks-board')->buildMenuItem();
        $menus['quick']['items'][] = $report->getExact('currenttasks')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('project-log')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('search')->buildMenuItem();
        $menus['quick']['items'][] = $module->getExact('components-list')->buildMenuItem();

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