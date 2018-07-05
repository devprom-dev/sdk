<?php

include_once "FunctionalAreaMenuProjectBuilder.php";

class FunctionalAreaMenuCommonBuilder extends FunctionalAreaMenuProjectBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);
    	
		$module = getFactory()->getObject('Module');
		$report = getFactory()->getObject('PMReport');
		
		$module_it = $module->getExact('tasks-board');
    	if ( $module_it->getId() != '' ) {
			$items = array();
      	    $items['mytasks'] = $report->getExact('mytasks')->buildMenuItem();
		 	$items['tasks-board'] = $module_it->buildMenuItem();

    		$menus['tasks'] = array(
    		   'name' => translate('Задачи'), 
    		   'items' => $items,
    		   'uid' => 'tasks' 
    		);
		}
		
		return $menus;
    }
}