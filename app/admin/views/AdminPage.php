<?php

include( dirname(__FILE__).'/AdminPageMenu.php');

class AdminPage extends Page
{
	function getRenderParms()
	{
		return array_merge( parent::getRenderParms(), array (
			'caption_template' => 'admin/PageTitle.php',
			'caption_data' => ''
		));
	}
	
	function getTabsTemplate()
	{
		return 'admin/PageTabs.php'; 	
	}
	
 	function getMenus()
 	{
 		global $plugins, $session;
 		
		$actions = $menus = array();

		$user_it = getSession()->getUserIt();
		
		if ( !is_object($user_it) ) return $actions;
		if ( $user_it->getId() < 1 ) return $actions;

 	 	// pluginnable quick menus
		$plugin_menus = $plugins->getHeaderMenus( 'admin' );
		
		foreach ( $plugin_menus as $menu )
		{
			$menus[] = array (
				'class' => 'header_popup',
				'button_class' => $menu['class'],
				'title' => $menu['caption'],
				'description' => $menu['title'],
				'url' => $menu['url'],
				'items' => $menu['actions'],
				'icon' => $menu['icon']
			);
		}
		
		$actions = array( array ( 
			'name' => translate('��������� �������'),
			'url' => '/profile' 
		));

		$profile_actions = $plugins->getProfileActions('admin', $user_it);
		
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

		$auth_factory = $session->getAuthenticationFactory();
		
		if ( $auth_factory->tokenRequired() )
		{
			if ( $actions[count($actions) - 1]['name'] != '' )
			{
				array_push( $actions, array() );
			}
				
			array_push( $actions, array ( 
				'name' => translate('�����'),
				'url' => '/logoff' 
			));
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
 		$menu = new AdminPageMenu();
 		
 		return $menu->getTabs();
 	}
 	
 	function getMetrics()
 	{
 		if ( !DeploymentState::IsInstalled() ) return '';
 		
 		return parent::getMetrics();
 	}
 	
 	function getCheckpointAlerts()
 	{
 		if ( !DeploymentState::IsInstalled() ) return array();
 		
 		return parent::getCheckpointAlerts();
 	}
 	
 	function getTitle()
 	{
 	    return '';
 	}
}