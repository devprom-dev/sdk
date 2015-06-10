<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuBuilder.php";

class FunctionalAreaMenuManagementResManBuilder extends FunctionalAreaMenuBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 		$menus = $set->getAreaMenus( FUNC_AREA_MANAGEMENT );
 		if ( count($menus) < 1 ) return;
 		
        $item = getFactory()->getObject('PMReport')->getExact('resourceusage')->buildMenuItem();
        $reports_menu = $menus['reports']['items'];
        
        $menus['reports']['items'] = array_merge( 
                array_slice($reports_menu, 0, count($reports_menu)-1), 
                array( $item ),
                array_slice($reports_menu, count($reports_menu)-1)
        );

 		$set->setAreaMenus( FUNC_AREA_MANAGEMENT, $menus );
    }
}