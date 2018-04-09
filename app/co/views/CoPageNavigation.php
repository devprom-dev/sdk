<?php
include "CoPageMenu.php";

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

        $quick_actions = $plugins->getQuickActions('co');
        if ( count($quick_actions) > 0 ) {
            foreach ( $quick_actions as $action ) {
                array_push( $actions, $action );
            }
        }

        if ( count($actions) > 0 ) {
            $menus[] = array (
                'class' => 'header_popup',
                'button_class' => 'btn-warning',
                'icon' => 'icon-plus icon-white',
                'items' => $actions,
                'id' => 'navbar-quick-create'
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
        $docs_url = defined('HELP_DOCS_URL') ? HELP_DOCS_URL : 'http://devprom.ru/docs';
        return array_merge(
            array(
                array (
                    'name' => text('guide.userdocs'),
                    'url' => $docs_url,
                    'target' => '_blank'
                )
            ),
            parent::getHelpActions()
        );
    }

    function getTabs()
    {
        if ( getSession()->getUserIt()->getId() < 1 ) return array();

        $menu = new CoPageMenu();
        return $menu->getTabs();
    }
}