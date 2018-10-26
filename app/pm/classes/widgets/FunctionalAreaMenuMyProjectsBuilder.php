<?php

class FunctionalAreaMenuMyProjectsBuilder extends FunctionalAreaMenuFavoritesBuilder
{
    public function build( FunctionalAreaMenuRegistry & $set )
    {
    	$menus = parent::build($set);

        $module = getFactory()->getObject('Module');

        $items = array();
        $item = $module->getExact('projects')->buildMenuItem();
        $items['projects'] = $item;
        $menus['quick']['items'] = array_merge($menus['quick']['items'], $items);

        $registry = getFactory()->getObject('Project')->getRegistry();
        $registry->setPersisters(array());
    	if ( $registry->Count() < 1 && !defined('SKIP_WELCOME_PAGE') )
    	{
			$menus['quick']['items'] = array_merge(
					array (
							array (
									'uid' => 'welcome',
									'url' => '/projects/welcome'
							)
					),
					$menus['quick']['items']
			);
    	} 

		$set->setAreaMenus( FUNC_AREA_FAVORITES, $menus );
		
		return $menus;
    }
}