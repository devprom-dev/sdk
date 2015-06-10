<?php

include_once SERVER_ROOT_PATH."pm/classes/widgets/FunctionalAreaMenuSettingsBuilder.php";

class FunctionalAreaMenuPermissionsSettingsBuilder extends FunctionalAreaMenuSettingsBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
 		$menus = $set->getAreaMenus( FunctionalAreaMenuSettingsBuilder::AREA_UID );
 		if ( count($menus) < 1 ) return;

        $menus['quick']['items'] = array_merge( 
                array_slice($menus['quick']['items'], 0, 1),
                array( 
               		'participants' => getFactory()->getObject('Module')->getExact('permissions/participants')->buildMenuItem()
        		),
                array_slice($menus['quick']['items'], 1)
        );
 				 		
        $menus = array_merge( 
                array_slice($menus, 0, 1),
                array( 
               		'permissions' => 
           				array (
	                		'name' => text('permissions2'),
           					'uid' => 'permissions',
	                		'items' => 
		                		array (
		                				getFactory()->getObject('Module')->getExact('permissions/settings')->buildMenuItem(),
		                				getFactory()->getObject('Module')->getExact('dicts-projectrole')->buildMenuItem()
		                		)
          				)
        		),
                array_slice($menus, 1)
        );
 		$set->setAreaMenus( FunctionalAreaMenuSettingsBuilder::AREA_UID, $menus );
    }
}