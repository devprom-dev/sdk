<?php
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class PMPageNavigation extends PageNavigation
{
    function getAreas()
    {
        $service = new WorkspaceService();
        $areas = $service->getFunctionalAreas();
        foreach( $areas['favs']['menus'] as $menu => $value ) {
            foreach( $areas['favs']['menus'][$menu]['items'] as $key => $value ) {
                $areas['favs']['menus'][$menu]['items'][$key]['entry-point'] = true;
                break;
            }
            break;
        }
        foreach( $areas as $key => $area ) {
            foreach ( $area['menus'] as $tab_key => $tab ) {
                foreach( $tab['items'] as $item_key => $item ) {
                    if ( $item['url'] == '' ) {
                        unset($areas[$key]['menus'][$tab_key]['items'][$item_key]);
                        continue;
                    }

                    $parts = preg_split('/\?/', str_replace(getSession()->getApplicationUrl(), '', $item['url']));
                    $areas[$key]['menus'][$tab_key]['items'][$item_key]['url'] =
                        trim($areas[$key]['menus'][$tab_key]['items'][$item_key]['url'], '&') . (count($parts) > 1 ? '&area='.$area['uid'] : '?area='.$area['uid']);
                }
                if ( count($tab['items']) < 1 ) unset($areas[$key]['menus'][$tab_key]);
            }
            if ( count($areas[$key]['menus']) < 1 ) unset($areas[$key]);
        }
        return $areas;
    }

    function getMenus()
    {
        $menus = array();

        $module = getFactory()->getObject('Module');
        $plugin_menus = getFactory()->getPluginsManager()->getHeaderMenus( 'pm' );
        foreach ( $plugin_menus as $menu )
        {
            $menus[] = array (
                'class' => 'header_popup',
                'button_class' => $menu['class'],
                'title' => $menu['caption'],
                'description' => $menu['title'],
                'url' => $menu['url'],
                'items' => $menu['actions'],
                'icon' => $menu['icon'],
                'id' => $menu['id']
            );
        }

        $actions = array();
        $actions[] = array (
            'name' => translate('Профиль пользователя'),
            'url' => '/profile'
        );

        if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
        $actions[] =  array (
            'name' => translate('Настройки'),
            'url' => getSession()->getApplicationUrl().'profile'
        );

        if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
        $actions[] = array (
            'name' => text(1811),
            'url' => $module->getExact('project-reports')->get('Url')
        );

        $auth_factory = getSession()->getAuthenticationFactory();
        if ( is_object($auth_factory) && $auth_factory->tokenRequired() )
        {
            if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();

            array_push( $actions, array (
                'name' => translate('Выйти'),
                'url' => '/logoff'
            ));
        }

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-file',
            'id' => 'menu-files',
            'url' => $module->getExact('attachments')->get('Url')
        );

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-question-sign',
            'id' => 'menu-guide',
            'items' => $this->getHelpActions()
        );

        $menus[] = $this->getProfileMenu(getSession()->getUserIt(), $actions);

        return $menus;
    }

    function getHelpActions()
    {
        $language = strtolower(getSession()->getLanguageUid());
        $docs_url = defined('HELP_DOCS_URL') ? HELP_DOCS_URL : 'http://devprom.ru/docs';
        return array_merge(
            array(
                array (
                    'name' => text(2277),
                    'uid' => 'shortcuts-help',
                    'click' => "javascript: workflowModify({'form_url':'/widget/shortcut/".$language."','class_name':'','entity_ref':'','object_id':'','form_title':'".text(2277)."','can_delete':'false','can_modify':'false','delete_reason':null}, donothing);",
                ),
                array(),
                array (
                    'name' => text('guide.tour'),
                    'click' => 'javascript:reStartTour();',
                ),
                array(),
                ($docs_url != ''
                    ? array (
                        'name' => text('guide.userdocs'),
                        'url' => $docs_url,
                        'target' => '_blank'
                    )
                    : array()
                )
            ),
            parent::getHelpActions()
        );
    }

    protected function getQuickActions()
    {
        $actions = array();
        $methodology_it = getSession()->getProjectIt()->getMethodologyIt();

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_ChangeRequest'));
        if ( $method->hasAccess() )
        {
            $method->setRedirectUrl('donothing');
            $type_it = getFactory()->getObject('pm_IssueType')->getRegistry()->Query(
                array (
                    new FilterBaseVpdPredicate()
                )
            );
            while ( !$type_it->end() ) {
                $actions[] = array (
                    'name' => translate($type_it->getDisplayName()),
                    'url' => $method->getJSCall(
                        array (
                            'Type' => $type_it->getId(),
                            'area' => $this->getPage()->getArea()
                        ),
                        translate($type_it->getDisplayName())
                    ),
                    'uid' => $type_it->get('ReferenceName')

                );
                $type_it->moveNext();
            }
            $actions[] = array (
                'name' => $method->getObject()->getDisplayName(),
                'url' => $method->getJSCall(
                    array (
                        'area' => $this->getPage()->getArea()
                    )
                ),
                'uid' => 'issue'
            );

            $template_it = getFactory()->getObject('RequestTemplate')->getAll();
            while( !$template_it->end() ) {
                $actions[] = array (
                    'name' => $template_it->getDisplayName(),
                    'url' => $method->getJSCall(
                        array (
                            'template' => $template_it->getId(),
                            'area' => $this->getPage()->getArea()
                        )
                    ),
                    'uid' => 'template'.$template_it->getId()
                );
                $template_it->moveNext();
            }
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Task'));
        if ( $methodology_it->HasTasks() && $method->hasAccess() )
        {
            $method->setRedirectUrl('donothing');
            $actions[] = array (
                'name' => $method->getObject()->getDisplayName(),
                'url' => $method->getJSCall(
                    array (
                        'Assignee' => !$methodology_it->IsParticipantsTakesTasks() ? getSession()->getUserIt()->getId() : '',
                        'area' => $this->getPage()->getArea()
                    )
                ),
                'uid' => 'task'
            );
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Feature'));
        if( $method->hasAccess() ) {
            $method->setRedirectUrl('donothing');
            $actions[] = array(
                'name' => translate('Функция'),
                'url' => $method->getJSCall(),
                'uid' => 'quick-feature'
            );
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Question'));
        if ( $method->hasAccess() )
        {
            $method->setRedirectUrl('donothing');
            $actions[] = array (
                'name' => $method->getObject()->getDisplayName(),
                'url' => $method->getJSCall(
                    array (
                        'area' => $this->getPage()->getArea()
                    )
                ),
                'uid' => 'question'
            );
        }

        $quick_actions = PluginsFactory::Instance()->getQuickActions('pm');
        if ( count($quick_actions) > 0 ) {
            foreach ( $quick_actions as $action ) {
                array_push( $actions, $action );
            }
        }

        $projectIt = getSession()->getProjectIt();
        if ( getFactory()->getAccessPolicy()->can_create($projectIt->object) ) {
            $actions[] = array();
            $actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  '/projects/welcome',
                'name' => text('project.new')
            );
        }

        return $actions;
    }

    function getProjectNavigationParms()
    {
        $parms = parent::getProjectNavigationParms();

        $project_it = getSession()->getProjectIt();
        if ( $project_it->IsPortfolio() ) {
            $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
            while ( !$portfolio_it->end() ) {
                if ( $project_it->getId() == $portfolio_it->getId() ) {
                    $project_it = $portfolio_it;
                    break;
                }
                $portfolio_it->moveNext();
            }
        }

        $parms['current_project'] = $project_it->get('CodeName');
        $parms['current_project_title'] = $project_it->getDisplayName();
        if ( $project_it->get('IsClosed') == 'Y' ) {
            $parms['current_project_title'] = '<strike>'.$parms['current_project_title'].'</strike>';
        }

        $portfolio_it = $project_it->copy();
        if ( !$project_it->IsPortfolio() && !$project_it->IsProgram() ) {
            $portfolio_it = $project_it->getParentIt();
        }

        $parms['subprojects_title'] = translate('project.name');
        if ( $portfolio_it->IsPortfolio() ) {
            $parms['portfolio_title'] = translate('Группа проектов');
        }
        elseif ( $portfolio_it->IsProgram() ) {
            $parms['portfolio_title'] = translate('Программа');
            $parms['subprojects_title'] = translate('Подпроект');
            $parms['program_actions'] = $this->getProgramNavitationActions($portfolio_it);
        }

        $parms['current_portfolio'] = $portfolio_it->get('CodeName');
        $parms['current_portfolio_title'] = $portfolio_it->getDisplayName();

        if ( $portfolio_it->get('CodeName') == 'my' ) {
            $parms['title'] = translate('Мои проекты');
        }

        $parms['project_actions'] = $this->getProjectNavigationActions($project_it);

        return $parms;
    }

    function getProgramNavitationActions($project_it)
    {
        $portfolio_actions = array();

        $item = getFactory()->getObject('Module')->getExact('project-reports')->buildMenuItem();
        $item['icon'] = 'icon-signal';
        $item['name'] = text(2194);
        $portfolio_actions[] = $item;

        if ( class_exists('ProjectLink') && getFactory()->getAccessPolicy()->can_create($project_it->object) ) {
            $portfolio_actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  '/projects/welcome?program='.$project_it->getId(),
                'name' => text('subproject.new')
            );
        }

        $portfolio_actions[] = array (
            'icon' => 'icon-wrench',
            'uid' => 'project-settings',
            'url' => getSession()->getApplicationUrl().'settings',
            'name' => text(2174)
        );
        return $portfolio_actions;
    }

    function getProjectNavigationActions($project_it)
    {
        $project_actions = array();

        $item = getFactory()->getObject('Module')->getExact('project-reports')->buildMenuItem();
        $item['icon'] = 'icon-signal';
        $item['name'] = text(2194);
        $project_actions[] = $item;

        if ( class_exists('ProjectLink') && getFactory()->getAccessPolicy()->can_create($project_it->object) ) {
            $project_actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  '/projects/welcome?program='.$project_it->getId(),
                'name' => text('subproject.new')
            );
        }

        $project_actions[] = array (
            'icon' => 'icon-wrench',
            'uid' => 'project-settings',
            'url' => getSession()->getApplicationUrl().'settings',
            'name' => text(2173)
        );

        return $project_actions;
    }

    function getInviteUserUrl()
    {
        if ( !defined('PERMISSIONS_ENABLED') ) return parent::getInviteUserUrl();

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
        if ( !$method->hasAccess() ) return parent::getInviteUserUrl();

        if ( !getSession()->getProjectIt()->IsPortfolio() ) {
            $method->setRedirectUrl("function(){javascript:window.location='".getFactory()->getObject('Module')->getExact('permissions/participants')->get('Url')."'}");
        }

        return $method->getJSCall();
    }
}