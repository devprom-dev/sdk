<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuKanbanBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    $this->report = getFactory()->getObject('PMReport');
		$this->module = getFactory()->getObject('Module');

 	    $project_it = getSession()->getProjectIt();
 	    if ( $project_it->object instanceof Portfolio || $project_it->object instanceof Program )
		{
			$this->buildPortfolioMenu($set);
		}
		else {
			$this->buildProjectMenu($set);
		}
	}

	protected function buildPortfolioMenu( & $set )
	{
		$ids = getSession()->getProjectIt()->getRef('LinkedProject')->fieldToArray('pm_ProjectId');
		$project_it = getFactory()->getObject('Project')->getRegistry()->Query(
				array (
						new FilterAttributePredicate('Tools', array('kanban_ru.xml','kanban_en.xml')),
						new FilterInPredicate($ids)
				)
		);
		if ( $project_it->count() != count($ids) ) return;

		$menus = $set->getAreaMenus( FUNC_AREA_FAVORITES );

		$menus['quick']['items']['issuesboard'] = $this->report->getExact('kanbanboard')->buildMenuItem();
		$menus['reports']['items'][] = $this->report->getExact('avgleadtime')->buildMenuItem();

		$set->setAreaMenus(FUNC_AREA_FAVORITES, $menus);
	}

	protected function buildProjectMenu( & $set )
	{
		$methodology_it = getSession()->getProjectIt()->getMethodologyIt();
		if ( $methodology_it->get('IsKanbanUsed') != 'Y' ) return;

		$settings_menu = $set->getAreaMenus( FUNC_AREA_MANAGEMENT );
		if ( count($settings_menu) > 0 ) {
			$set->setAreaMenus( FUNC_AREA_MANAGEMENT, array() );
		}

		$menu = $set->getAreaMenus( FUNC_AREA_FAVORITES );
		unset($menu['quick']['items']['project-log']);

		// quick
		$items = array();
		$item = $this->report->getExact('kanbanboard')->buildMenuItem();
		$item['order'] = 5;
		$items['board'] = $item;
		$item = $this->module->getExact('project-knowledgebase')->buildMenuItem();
		$item['order'] = 9999;
		$items['knowledgebase'] = $item;
		$menu['quick']['items'] = array_merge($items, $menu['quick']['items']);

		// reports
		$items = array();
		$items['comulativeflow'] = $this->report->getExact('commulativeflow')->buildMenuItem();
		$items['avgleadtime'] = $this->report->getExact('avgleadtime')->buildMenuItem();
		$items['activity'] = $this->report->getExact('project-log')->buildMenuItem();
		$items['activity']['name'] = text('kanban20');
		$menu['reports'] = array (
				'name' => translate('Отчеты'),
				'uid' => 'reports',
				'items' => $items
		);

		$items = array();
		$item = $this->module->getExact('dicts-requesttype')->buildMenuItem();
		$item['name'] = text('kanban21');
		$items[] = $item;

		if ( $methodology_it->HasTasks() )
		{
			$item = $this->module->getExact('dicts-tasktype')->buildMenuItem();
			$item['name'] = text('kanban28');
			$items[] = $item;
		}
		$item = $this->module->getExact('workflow-issuestate')->buildMenuItem();
		$item['name'] = text('kanban22');
		$items[] = $item;
		$item = $this->module->getExact('dicts-requesttemplate')->buildMenuItem();
		$item['name'] = text('kanban25');
		$items[] = $item;
		$menu['settings'] = array (
				'name' => translate('Настройки'),
				'uid' => 'settings',
				'items' => $items
		);

		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menu );
	}

	private $report = null;
	private $module = null;
}