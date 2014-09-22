<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuCommonBuilder.php";

class FunctionalAreaMenuDevelopmentBuilder extends FunctionalAreaMenuCommonBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
        global $model_factory, $project_it;
        	
        $menu = parent::build($set);
         
        $module = $model_factory->getObject('Module');
        
        $report = $model_factory->getObject('PMReport');
        
        $menu['quick']['items'][] = $module->getExact('sourcecontrol/revision')->buildMenuItem(); 
        
        $menu['quick']['items'][] = $module->getExact('sourcecontrol/files')->buildMenuItem(); 
        
 		// charts tab
 		
        $items = array();
        	
        $items['commitsfreqperauthors'] = $report->getExact('commitsfreqperauthors')->buildMenuItem();
        
        $items['project-reports'] = $module->getExact('project-reports')
        		->buildMenuItem('&pmreportcategory=dev&'.ModuleCategoryBuilderCode::AREA_UID);
        
        $items['project-reports']['name'] = translate('Все отчеты');
        
 		$menu[] = array ( 
 		    'module' => 'files', 
			'name' => translate('Графики и отчеты'), 
			'items' => $items 
 		);

        // settings tab
        
        $items = array();
        	
        $items[] = $module->getExact('sourcecontrol/connection')->buildMenuItem();
        
 		$menu[] = array ( 
 		    'module' => 'code', 
			'name' => translate('Настройки'), 
			'items' => $items 
 		);
 		
 		$set->setAreaMenus( ModuleCategoryBuilderCode::AREA_UID, $menu );
    }
}