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
        $item = $this->module->getExact('projects')->buildMenuItem();
        $item['order'] = 5;
        $items['projects'] = $item;

        $planItem = $this->report->getExact('projectplan')->buildMenuItem();
        $planItem['order'] = 6;
        $items['plan'] = $planItem;

		$item = $this->module->getExact('issues-board')->buildMenuItem();
		$item['order'] = 7;
		$items['issues-board'] = $item;

		$items[] = $this->report->getExact('discussions')->buildMenuItem();
        $items['knowledgebase'] = $this->module->getExact('project-knowledgebase')->buildMenuItem();
        $items['whatsnew'] = $this->module->getExact('whatsnew')->buildMenuItem();
        $items['project-log'] = $this->report->getExact('project-log')->buildMenuItem();
        $items['mytasks'] = $this->report->getExact('mytasks')->buildMenuItem();
        $items['attachments'] = $this->module->getExact('attachments')->buildMenuItem();

		$menus['quick']['items'] = array_merge($menus['quick']['items'], $items);

		// plan items
		$this->buildPlansFolder($menus);

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
 	        'name' => translate('Дополнительно'),
            'uid' => 'resources',
            'items' => array(
                $this->module->getExact('tasks-board')->buildMenuItem(),
                'activitiesreport' => $this->report->getExact('activitiesreport')->buildMenuItem('group=SystemUser'),
            )
 	    );
    }

	private $report = null;
    private $module = null;
}