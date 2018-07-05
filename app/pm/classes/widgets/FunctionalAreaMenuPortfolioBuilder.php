<?php
include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuPortfolioBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
		$this->report = getFactory()->getObject('PMReport');
        $this->module = getFactory()->getObject('Module');

 	    $menus = parent::build($set);
 	    
		$items = array();
		$item = $this->module->getExact('issues-board')->buildMenuItem();
		$item['order'] = 5;
		$items['issuesboard'] = $item;
		$items[] = $this->report->getExact('discussions')->buildMenuItem();
        $items['knowledgebase'] = $this->module->getExact('project-knowledgebase')->buildMenuItem();
        $items['whatsnew'] = $this->module->getExact('whatsnew')->buildMenuItem();
		$menus['quick']['items'] = array_merge($menus['quick']['items'], $items);

		// plan items
		$this->buildPlansFolder($menus);

		// documents items
		$this->buildDocumentsFolder( $menus );

		// reports items
		$this->buildReportsFolder( $menus );

		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
    }

	protected function buildDocumentsFolder( &$menus )
	{
		$menus['documents'] = array (
				'name' => translate('Документы'),
				'uid' => 'documents',
				'items' => array()
		);
	}

    protected function buildPlansFolder( &$menus )
    {
    	$menus['resources'] = array (
 	        'name' => translate('Планы'),
            'uid' => 'resources',
            'items' => array(
                $this->report->getExact('projectplan')->buildMenuItem(),
                $this->module->getExact('tasks-board')->buildMenuItem()
			)
 	    );
    }

	protected function buildReportsFolder( &$menus )
	{
		$menus['reports'] = array (
				'name' => translate('Отчеты'),
				'uid' => 'reports',
				'items' => array(
						'activitiesreport' => $this->report->getExact('activitiesreport')->buildMenuItem('group=SystemUser'),
						'project-log' => $this->report->getExact('project-log')->buildMenuItem()
				)
		);
	}

	private $report = null;
}