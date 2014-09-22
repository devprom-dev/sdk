<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuCommonBuilder.php";

class FunctionalAreaMenuFileServerBuilder extends FunctionalAreaMenuCommonBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 	    $menu = parent::build($set);
 	    
 	    $module = getFactory()->getObject('Module');
 	    
		// files tab
		
		$items = array();

		$items[] = getFactory()->getObject('PMReport')->getExact('fileserverfiles')->buildMenuItem();
		
		$items[] = $module->getExact('fileserver/folders')->buildMenuItem();
		
 		$menu[] = array ( 
			'name' => translate('װאיכû.'), 
 		    'uid' => 'files',
			'items' => $items 
 		);
        
 		$set->setAreaMenus( ModuleCategoryBuilderFileServer::AREA_UID, $menu );
    }
}