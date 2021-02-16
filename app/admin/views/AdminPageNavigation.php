<?php
include "AdminPageMenu.php";

class AdminPageNavigation extends PageNavigation
{
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
            'name' => translate('Настроить профиль'),
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
                'name' => translate('Выйти'),
                'url' => '/logoff'
            ));
        }

        $menus[] = array (
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-question-sign',
            'id' => 'menu-guide',
            'items' => $this->getHelpActions()
        );

        $menus[] = $this->getProfileMenu($user_it, $actions);

        return $menus;
    }

    function getHelpActions()
    {
        return array_merge(
            array(
                array (
                    'name' => text('guide.userdocs'),
                    'url' => \EnvironmentSettings::getHelpDocsUrl('4715.html'),
                    'target' => '_blank'
                )
            ),
            parent::getHelpActions()
        );
    }

    function getTabs()
    {
        $menu = new AdminPageMenu();
        return $menu->getTabs();
    }
}