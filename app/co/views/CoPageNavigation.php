<?php

class CoPageNavigation extends PageNavigation
{
    function build()
    {
        if ( getSession()->getUserIt()->getId() < 1 ) return array();
        return parent::build();
    }

    function getMenus()
    {
        $session = getSession();
        $plugins = PluginsFactory::Instance();
        $user_it = $session->getUserIt();

        $menus = array();
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
                'items' => $menu['actions'],
                'icon' => $menu['icon']
            );
        }

        $menus[] = array (
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-question-sign',
            'id' => 'menu-guide',
            'items' => $this->getHelpActions()
        );

        // profile actions
        $actions = array(
            array ( 'name' => translate('Настроить профиль'),
                'url' => '/profile' )
        );

        $profile_actions = $plugins->getProfileActions('co', $user_it);
        if ( count($profile_actions) > 0 )
        {
            if ( $actions[count($actions) - 1]['name'] != '' ) {
                array_push( $actions, array() );
            }
            foreach ( $profile_actions as $action ) {
                array_push( $actions, $action );
            }
        }

        if ( $session->getAuthenticationFactory()->tokenRequired() )
        {
            if ( $actions[count($actions) - 1]['name'] != '' ) {
                array_push( $actions, array() );
            }
            $actions[] = array (
                'name' => translate('Выйти'),
                'url' => '/logoff'
            );
        }

        $menus[] = $this->getProfileMenu($user_it, $actions);

        return $menus;
    }

    function getHelpActions()
    {
        return array_merge(
            array(
                array (
                    'name' => text('guide.userdocs'),
                    'url' => \EnvironmentSettings::getHelpDocsUrl(),
                    'target' => '_blank'
                )
            ),
            parent::getHelpActions()
        );
    }

    function getTabs()
    {
        $pages = array();
        if ( getSession()->getUserIt()->getId() < 1 ) return $pages;

        $portfolio = defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ? 'my' : 'all';

        $pages['main'] = array(
            'uid' => 'main',
            'items' => array(
                array( 'url' => '/profile', 'name' => translate('Профиль'), 'uid' => 'profile' ),
                array( 'url' => '/notifications', 'name' => text(1912), 'uid' => 'notifications' ),
                array( 'url' => '/keys', 'name' => text(2913), 'uid' => 'keys' ),
                array( 'url' => "/pm/{$portfolio}/mytasks", 'name' => text(3141), 'uid' => 'mytasks' )
            )
        );

        $plugins = getFactory()->getPluginsManager();
        $tabs = is_object($plugins) ? $plugins->getHeaderTabs(getSession()->getSite()) : array();
        foreach( $tabs as $tab ) {
            if ( $tab['uid'] == 'main' ) {
                $pages['main']['items'] = array_merge($pages['main']['items'], $tab['items']);
            }
        }

        return $pages;
    }

    function getQuickActions()
    {
        $actions = parent::getQuickActions();

        $plugins = PluginsFactory::Instance();
        $quick_actions = $plugins->getQuickActions('co');
        if ( count($quick_actions) > 0 ) {
            foreach ( $quick_actions as $action ) {
                array_push( $actions, $action );
            }
        }

        $projectActions = array();
        if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) )
        {
            $projectActions[] = array (
                'icon' => 'icon-plus',
                'url' =>  '/projects/welcome',
                'name' => text('project.name')
            );
        }
        if ( count($projectActions) > 0 ) {
            $actions = array_merge($actions, array(array()), $projectActions);
        }

        return $actions;
    }
}