<?php
use Devprom\ProjectBundle\Service\Navigation\WorkspaceService;

class PMPageNavigation extends PageNavigation
{
    function build()
    {
        $parms = parent::build();

        $parms['adjust_menu'] = getFactory()->getObject('PMReport')
            ->getExact('navigation-settings')->buildMenuItem('area=favs');

        $projectIt = getSession()->getProjectIt();
        if ( $projectIt->IsPortfolio() ) {
            $portfolio = getFactory()->getObject('co_ProjectGroup');
            if ( getFactory()->getAccessPolicy()->can_modify($portfolio) && getFactory()->getAccessPolicy()->can_modify($projectIt) )
            {
                $method = new ObjectModifyWebMethod(
                    $portfolio->getExact($projectIt->get('ProjectGroupId'))
                );
                $parms['settings_menu'] = array (
                    'icon' => 'icon-briefcase',
                    'uid' => 'settings-4-project',
                    'url' => $method->getJSCall(),
                    'name' => translate('Настройки')
                );
            }
        }
        else {
            $parms['settings_menu'] = array (
                'icon' => 'icon-wrench',
                'uid' => 'settings-4-project',
                'url' => getSession()->getApplicationUrl().'settings',
                'name' => translate('Настройки')
            );
        }

        return $parms;
    }

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
            'name' => text(1292),
            'url' => getSession()->getApplicationUrl().'profile'
        );

        if ( $actions[count($actions)-1]['name'] != '' ) $actions[] = array();
        $actions['profile-my-reports'] = array (
            'name' => text(1811),
            'url' => $module->getExact('project-reports')->get('Url'),
            'uid' => 'profile-my-reports'
        );

        $auth_factory = getSession()->getAuthenticationFactory();
        if ( is_object($auth_factory) && $auth_factory->tokenRequired() )
        {
            $actions[] = array();
            array_push( $actions, array (
                'name' => translate('Выйти'),
                'url' => '/logoff'
            ));
        }

        $portfolioIt = getFactory()->getObject('Portfolio')->getAll();
        while( !$portfolioIt->end() ) {
            if ( in_array($portfolioIt->get('CodeName'), array('my','all')) ) {
                break;
            }
            $portfolioIt->moveNext();
        }

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-eye-open',
            'id' => 'menu-changelog',
            'url' => $module->getExact('project-log')->getUrl(),
            'description' => text(2624)
        );

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-book',
            'id' => 'menu-kbs',
            'url' => '/pm/' . ($portfolioIt->getId() != '' ? $portfolioIt->get('CodeName') : getSession()->getProjectIt()->get('CodeName')). '/knowledgebase/tree',
            'description' => translate('База знаний')
        );

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-file',
            'id' => 'menu-files',
            'url' => $module->getExact('attachments')->get('Url'),
            'description' => text(2635)
        );

        $menus[] = array (
            'class' => 'header_popup',
            'button_class' => 'btn-navbar btn-link',
            'icon' => 'icon-white icon-question-sign',
            'id' => 'menu-guide',
            'items' => $this->getHelpActions(),
            'description' => text(2636)
        );

        $menus[] = $this->getProfileMenu(getSession()->getUserIt(), $actions);

        return $menus;
    }

    function getHelpActions()
    {
        $language = strtolower(getSession()->getLanguageUid());
        $docs_url = \EnvironmentSettings::getHelpDocsUrl();

        $docsMap = \EnvironmentSettings::getProcessDocsMap();
        $processDocsUrl = $docsMap[getSession()->getProjectIt()->get('Tools')];

        return array_merge(
            array(
                array (
                    'name' => text(2277),
                    'uid' => 'shortcuts-help',
                    'click' => "javascript: workflowModify({'form_url':'/widget/shortcut/".$language."','class_name':'','entity_ref':'','object_id':'','can_delete':'false','can_modify':'false','delete_reason':null}, donothing);",
                ),
                array(),
                array (
                    'name' => text('guide.tour'),
                    'click' => 'javascript:reStartTour();',
                ),
                array(),
                ($processDocsUrl != ''
                    ? array (
                        'name' => text('guide.process'),
                        'url' => $processDocsUrl,
                        'target' => '_blank'
                    )
                    : array()
                ),
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

        $className = getFactory()->getClass('Issue');
        if (getSession()->IsRDD()) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject($className));
            if ($method->hasAccess()) {
                $actions[] = array(
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall(
                        array(
                            'area' => $this->getPage()->getArea()
                        ),
                        $method->getCaption()
                    ),
                    'uid' => 'request'
                );
            }
        }

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Request'));
        if ( $method->hasAccess() ) {
            $type_it = getFactory()->getObject('RequestType')->getRegistry()->Query(
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
                    'uid' => $type_it->get('ReferenceName') == '' ? 'issue' : $type_it->get('ReferenceName')

                );
                $type_it->moveNext();
            }

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
        if ( $methodology_it->HasTasks() && $method->hasAccess() ) {
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

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('pm_Question'));
        if ( $method->hasAccess() ) {
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

        $projectActions = array();

        $inviteUrl = $this->getInviteUserUrl();
        if ( $inviteUrl != '' ) {
            $projectActions[] =
                array(
                    'url' => $inviteUrl,
                    'name' => translate('Участник')
                );
        }

        $module = getFactory()->getObject('Module');

        $projectIt = getSession()->getProjectIt();
        if ( getFactory()->getAccessPolicy()->can_create($projectIt->object) )
        {
            $url = '/projects/welcome';
            $projectIt = getSession()->getProjectIt();
            if ( $projectIt->IsPortfolio() && $projectIt->get('ProjectGroupId') != '' ) {
                $url .= '?portfolio='.$projectIt->get('ProjectGroupId');
            }

            $projectActions[] = array (
                'icon' => 'icon-plus',
                'url' => $url,
                'name' => text('project.name')
            );

            if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED && !$projectIt->IsPortfolio() ) {
                $projectActions[] = array();
                $projectActions[] = array (
                    'icon' => 'icon-plus',
                    'uid' => 'new-subproject',
                    'url' =>  '/projects/welcome?program='.$projectIt->getId(),
                    'name' => text('subproject.name')
                );
                $projectActions[] = array (
                    'icon' => 'icon-plus',
                    'url' =>  $module->getExact('ee/projectlinks')->getUrl(),
                    'name' => text('subproject.include')
                );
            }
        }

        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED )
        {
            $portfolio = getFactory()->getObject('co_ProjectGroup');
            if ( getFactory()->getAccessPolicy()->can_create($portfolio) )
            {
                $method = new ObjectCreateNewWebMethod($portfolio);
                $method->setRedirectUrl('function(id){window.location=\'/pm/project-portfolio-\'+id;}');
                $projectActions[] = array();
                $projectActions[] = array (
                    'icon' => 'icon-briefcase',
                    'url' => $method->getJSCall(),
                    'name' => text('portfolio.name')
                );
            }
        }

        if ( count($projectActions) > 0 ) {
            $actions = array_merge($actions, array(array()), $projectActions);
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

        $parentIt = $project_it->getParentIt();
        $parms['current_project'] = $project_it->get('CodeName');
        $parms['current_project_title'] = $parentIt->getId() != '' && !in_array($parentIt->get('CodeName'), array('my','all'))
            ? $parentIt->getDisplayName() . '&nbsp; &#x279E; &nbsp;' . $project_it->getDisplayName()
            : $project_it->getDisplayName();

        if ( $project_it->get('IsClosed') == 'Y' ) {
            $parms['current_project_title'] = '<strike>'.$parms['current_project_title'].'</strike>';
        }
        return $parms;
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