<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuScrumBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
        $project_it = getSession()->getProjectIt();
        
        $methodology_it = $project_it->getMethodologyIt();

        if ( $methodology_it->get('UseScrums') != 'Y' ) return;
        
        if ( is_a($project_it->object, 'Portfolio') || is_a($project_it->object, 'Program') ) return;
        
        $this->buildManagement( $set );
        $this->buildFavorites( $set );
    }
    
    function buildFavorites( $set )
    {
        $module = getFactory()->getObject('Module');
        $report = getFactory()->getObject('PMReport');
        
 	    $menu = $set->getAreaMenus( FUNC_AREA_FAVORITES );
        unset($menu['quick']['items']['project-log']);
        
 	    $items = array();
 	    
 	    $items['board'] = $module->getExact('issues-board')->buildMenuItem();
		$items['backlog'] = $report->getExact('productbacklog')->buildMenuItem();
		$items['plan'] = $module->getExact('project-plan-hierarchy')->buildMenuItem();
 	    $items['tasks'] = $module->getExact('tasks-board')->buildMenuItem();
 	    
 	    $item = $module->getExact('project-knowledgebase')->buildMenuItem();
 	    $item['order'] = 9999;
		$items['knowledgebase'] = $item;
 	    
		$menu['quick']['items'] = array_merge($items, $menu['quick']['items']); 
		
		$items = array();
		
		$items['burndown'] = $report->getExact('releaseburndown')->buildMenuItem();
 	    $items['burndown']['name'] = text('scrum8');
		$items['velocity'] = $report->getExact('velocitychart')->buildMenuItem();
		$items['burnup'] = $report->getExact('projectburnup')->buildMenuItem();
		$items['activity'] = $report->getExact('project-log')->buildMenuItem();
 	    $items['activity']['name'] = text('scrum5');
 	    
        $items['mettings'] = $module->getExact('scrum/meetings')->buildMenuItem();
 	    
 	    $menu['reports'] = array (
 	        'name' => translate('Отчеты'),
 	        'uid' => 'reports',
 	        'items' => $items
 	    );

		$items = array();
		
		$item = $module->getExact('dicts-requesttype')->buildMenuItem();
		$item['name'] = text('scrum6'); 
		$items[] = $item;
		
		$item = $module->getExact('workflow-issuestate')->buildMenuItem();
		$item['name'] = text('scrum7'); 
		$items[] = $item;
		
 	    $menu['settings'] = array (
 	            'name' => translate('Настройки'),
 	            'uid' => 'settings',
 	            'items' => $items
 	    );
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menu );        
    }
    
    function buildManagement( $set )
    {
 	    $settings_menu = $set->getAreaMenus( FUNC_AREA_MANAGEMENT );
 	    if ( count($settings_menu) < 1 ) return;
 	    
 	    $set->setAreaMenus( FUNC_AREA_MANAGEMENT, array() );
    }
}