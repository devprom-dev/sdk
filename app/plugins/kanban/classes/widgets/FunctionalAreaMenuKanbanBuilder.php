<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuKanbanBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    global $model_factory;

 	    $project_it = getSession()->getProjectIt();
 	    
 		$methodology_it = $project_it->getMethodologyIt();
 	    
 	    if ( $methodology_it->get('IsKanbanUsed') != 'Y' ) return;

 	    if ( is_a($project_it->object, 'Portfolio') || is_a($project_it->object, 'Program') ) return;
 	    
 	    $settings_menu = $set->getAreaMenus( FUNC_AREA_MANAGEMENT );
 	    
 	    if ( count($settings_menu) < 1 ) return;
 	    
 	    $set->setAreaMenus( FUNC_AREA_MANAGEMENT, array() );
 	    
 	    $menu = $set->getAreaMenus( FUNC_AREA_FAVORITES );
        unset($menu['quick']['items']['project-log']);
 	    
 	    // quick
 		$items = array();
 	    
 		$item = $model_factory->getObject('PMReport')->getExact('kanbanboard')->buildMenuItem();
 		$item['order'] = 5;
 	    $items['board'] = $item;
 	    
 	    $item = $model_factory->getObject('Module')->getExact('project-knowledgebase')->buildMenuItem();
 	    $item['order'] = 9999;
 	    $items['knowledgebase'] = $item;
 	    
		$menu['quick']['items'] = array_merge($items, $menu['quick']['items']); 

		// reports
		$items = array();
		
		$items['comulativeflow'] = $model_factory->getObject('PMReport')->getExact('commulativeflow')->buildMenuItem();

		$items['avgleadtime'] = $model_factory->getObject('PMReport')->getExact('avgleadtime')->buildMenuItem();

 	    $items['activity'] = $model_factory->getObject('PMReport')->getExact('project-log')->buildMenuItem();
 	    $items['activity']['name'] = text('kanban20');
 	    
 	    $menu['reports'] = array (
 	        'name' => translate('Отчеты'),
 	        'uid' => 'reports',
 	        'items' => $items
 	    );

		$items = array();
		
		$item = getFactory()->getObject('Module')->getExact('dicts-requesttype')->buildMenuItem();
		$item['name'] = text('kanban21');

		$items[] = $item;
		
		if ( $methodology_it->HasTasks() )
		{
			$item = getFactory()->getObject('Module')->getExact('dicts-tasktype')->buildMenuItem();
			$item['name'] = text('kanban28');
	
			$items[] = $item;
		}

		$item = getFactory()->getObject('Module')->getExact('workflow-issuestate')->buildMenuItem();
		$item['name'] = text('kanban22');

		$items[] = $item;

		$item = getFactory()->getObject('Module')->getExact('dicts-requesttemplate')->buildMenuItem();
		$item['name'] = text('kanban25');

		$items[] = $item;
		
 	    $menu['settings'] = array (
 	            'name' => translate('Настройки'),
 	            'uid' => 'settings',
 	            'items' => $items
 	    );
		
		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menu );
    }
}