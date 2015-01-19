<?php

include ('CoPageMenu.php');
include ('CoPageTable.php');

class CoPage extends Page
{
	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array (
			'caption_template' => 'co/PageTitle.php',
			'caption_data' => $this->getCaption() 
		));
	}
	
	function getTabsTemplate()
	{
		return 'co/PageTabs.php'; 	
	}
	
	function getCaption()
 	{
		$settings = new Metaobject('cms_SystemSettings');
		
		$settings_it = $settings->getAll();

		return $settings_it->get('Caption');
 	}
 	
	function getMenus()
 	{
 		global $plugins, $session, $model_factory;
 		
 		$menus = array();
 		
 		$user_it = getSession()->getUserIt();

 		if( $user_it->getId() < 1 ) return $menus; 

 		// quick menu actions
		$actions = array();

 		// pluginnable quick menus
		$plugin_menus = $plugins->getHeaderMenus( 'co' );
		
		foreach ( $plugin_menus as $menu )
		{
			$menus[] = array (
				'class' => 'header_popup',
				'button_class' => $menu['class'],
				'title' => $menu['caption'],
				'description' => $menu['title'],
				'url' => $menu['url'],
				'items' => $menu['actions']
			);
		}
		
		$quick_actions = $plugins->getQuickActions('co');
		
		if ( count($quick_actions) > 0 )
		{
			foreach ( $quick_actions as $action )
			{
				array_push( $actions, $action );
			}
		}

		if ( count($actions) > 0 )
		{
			$menus[] = array (
				'class' => 'header_popup',
				'title' => translate('Создать'),
				'items' => $actions
			);
		}
 		
		// profile actions
		$actions = array(
			array ( 'name' => translate('Настроить профиль'),
					'url' => '/profile' )						
		);
		
		$profile_actions = $plugins->getProfileActions('co', $user_it);
		if ( count($profile_actions) > 0 )
		{
			if ( $actions[count($actions) - 1]['name'] != '' )
			{
				array_push( $actions, array() );
			}
			
			foreach ( $profile_actions as $action )
			{
				array_push( $actions, $action );
			}
		}

		$session = getSession();

		$auth_factory = $session->getAuthenticationFactory();
		 
		if ( $auth_factory->tokenRequired() )
		{
			if ( $actions[count($actions) - 1]['name'] != '' )
			{
				array_push( $actions, array() );
			}
			
			array_push( $actions, 
				array ( 'name' => translate('Выйти'),
						'url' => '/logoff' ) );
		}

		$menus[] = array (
			'class' => 'header_popup',
			'title' => $user_it->getDisplayName(),
			'items' => $actions
		);

		return $menus;
 	}
 	
 	function getTabs()
 	{
 	    if ( getSession()->getUserIt()->getId() < 1 ) return array();
 	    
 		$menu = new CoPageMenu();
 		
 		return $menu->getTabs();
 	}
}