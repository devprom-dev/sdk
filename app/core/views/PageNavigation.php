<?php
// PHPLOCKITOPT NOENCODE
// PHPLOCKITOPT NOOBFUSCATE

class PageNavigation
{
    protected $parms = array();

    function __construct( Page $page )
    {
        $this->page = $page;
        $this->parms = $this->build();
    }

    function __sleep() {
        return array('parms');
    }

    function getParms() {
        return $this->parms;
    }

    function getPage() {
        return $this->page;
    }

    protected function build()
    {
        return array (
            'areas' => $this->getAreas(),
            'menus' => $this->getMenus(),
            'project_navigation_parms' => $this->getProjectNavigationParms(),
            'company_name' => getFactory()->getObject('cms_SystemSettings')->getAll()->get('Caption'),
            'language_code' => strtolower(getSession()->getLanguageUid()),
            'quickMenu' => array (
                'class' => 'header_popup',
                'button_class' => 'btn-warning',
                'icon' => 'icon-plus icon-white',
                'items' => $this->getQuickActions(),
                'id' => 'navbar-quick-create'
            )
        );
    }

    protected function getQuickActions() {
        return array();
    }

    function getAreas()
    {
        $areas['main'] = array(
            'name' => 'default',
            'uid' => 'main',
            'menus' => $this->getTabs()
        );

        return $areas;
    }

    function getTabs() {
        return array();
    }

    function getMenus() {
        return array();
    }

    function getProfileMenu( $user_it, $actions )
    {
        $size = 30;
        $sprites_on_row = floor(32767 / $size);
        $row = floor($user_it->getId() / $sprites_on_row);
        $column = $user_it->getId() - $row * $sprites_on_row - 1;
        $timestamp = filemtime(SERVER_ROOT_PATH."images/userpics-middle.png");

        return array (
            'class' => 'header_popup',
            'title' => '<span class="profile-avatar" style="background: url(\'/images/userpics-middle.png?v='.$timestamp.'\') no-repeat -'.($column * $size).'px -'. ($row * $size).'px;"></span>',
            'items' => $actions,
        );
    }

    function getHelpActions()
    {
        $support_url = defined('HELP_SUPPORT_URL') ? HELP_SUPPORT_URL : 'http://support.devprom.ru/issue/new';
        if ( $support_url == '' ) return array();
        return array(
            array(),
            array (
                'name' => text('guide.support'),
                'url' => $support_url,
                'target' => '_blank'
            )
        );
    }

    function getProjectNavigationParms()
    {
        $programs = array();
        $projects = array();

        if ( getFactory()->getObject('User')->getAttributeType('GroupId') != '' )
        {
            $program_it = getFactory()->getObject('Program')->getAll();

            while ( !$program_it->end() )
            {
                $query_parms = array (
                    new ProjectStatePredicate('active'),
                    new FilterInPredicate(preg_split('/,/', $program_it->get('LinkedProject'))),
                    new SortAttributeClause('Importance'),
                    new SortAttributeClause('Caption')
                );

                if ( $program_it->get('IsParticipant') < 1 )
                {
                    $query_parms[] = new ProjectParticipatePredicate();
                }

                $linked_it = $program_it->get('LinkedProject') != ''
                    ? getFactory()->getObject('Project')->getRegistry()->Query($query_parms)
                    : getFactory()->getObject('Project')->getEmptyIterator();

                while ( !$linked_it->end() )
                {
                    if ( $program_it->getId() == $linked_it->getId() )
                    {
                        $linked_it->moveNext();
                        continue;
                    }

                    $projects[$program_it->get('CodeName')][$linked_it->get('CodeName')] = array (
                        'name' => $linked_it->getDisplayName(),
                        'url' => '/pm/'.$linked_it->get('CodeName')
                    );

                    $linked_it->moveNext();
                }

                if ( count($projects[$program_it->get('CodeName')]) > 0 )
                {
                    $programs[$program_it->get('CodeName')] = array (
                        'name' => $program_it->getDisplayName(),
                        'url' => '/pm/'.$program_it->get('CodeName')
                    );
                }

                $program_it->moveNext();
            }
        }

        $portfolios = array();

        $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
        while ( !$portfolio_it->end() )
        {
            if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) )
            {
                $portfolio_it->moveNext(); continue;
            }

            if ( $portfolio_it->get('CodeName') != 'all' || !defined('PERMISSIONS_ENABLED') )
            {
                $linked_it = $portfolio_it->get('LinkedProject') != ''
                    ? getFactory()->getObject('Project')->getRegistry()->Query(
                        array (
                            new ProjectStatePredicate('active'),
                            new FilterInPredicate(preg_split('/,/', $portfolio_it->get('LinkedProject'))),
                            new SortAttributeClause('Importance'),
                            new SortAttributeClause('Caption')
                        )
                    )
                    : getFactory()->getObject('Project')->getEmptyIterator();

                while ( !$linked_it->end() ) {
                    if ( $portfolio_it->getId() == $linked_it->getId() || array_key_exists($linked_it->get('CodeName'), $programs) ) {
                        $linked_it->moveNext();
                        continue;
                    }
                    $projects[$portfolio_it->get('CodeName')][$linked_it->get('CodeName')] = array (
                        'name' => $linked_it->getDisplayName(),
                        'url' => '/pm/'.$linked_it->get('CodeName')
                    );
                    $linked_it->moveNext();
                }
            }

            if ( in_array($portfolio_it->get('CodeName'), array('all', 'my')) || count($projects[$portfolio_it->get('CodeName')]) > 0 )
            {
                $portfolios[$portfolio_it->get('CodeName')] = array (
                    'name' => $portfolio_it->getDisplayName(),
                    'url' => '/pm/'.$portfolio_it->get('CodeName')
                );
            }

            $portfolio_it->moveNext();
        }

        if ( !defined('PERMISSIONS_ENABLED') ) {
            $linked_it = getFactory()->getObject('Project')->getRegistry()->Query(
                array(
                    new ProjectStatePredicate('active'),
                    new SortAttributeClause('Importance'),
                    new SortAttributeClause('Caption')
                )
            );
        }
        else {
            $linked_it = getFactory()->getObject('Project')->getRegistry()->Query(
                array (
                    new ProjectParticipatePredicate(),
                    new ProjectStatePredicate('active'),
                    new SortAttributeClause('Importance'),
                    new SortAttributeClause('Caption')
                )
            );
        }
        while ( !$linked_it->end() )
        {
            $projects[''][$linked_it->get('CodeName')] = array (
                'name' => $linked_it->getDisplayName(),
                'url' => '/pm/'.$linked_it->get('CodeName')
            );
            $linked_it->moveNext();
        }

        foreach( $programs as $program_id => $program ) {
            unset( $projects['my'][$program_id] );
            unset( $projects['all'][$program_id] );
            unset( $projects[''][$program_id] );
            if ( !is_array($projects[$program_id]) ) continue;
            foreach( $projects[$program_id] as $project_id => $project ) {
                unset( $projects['my'][$project_id] );
                unset( $projects['all'][$project_id] );
                unset( $projects[''][$project_id] );
            }
        }
        foreach( $portfolios as $portfolio_id => $portfolio ) {
            if ( in_array($portfolio_id, array('my','all')) ) continue;
            if ( !is_array($projects[$portfolio_id]) ) continue;
            foreach( $projects[$portfolio_id] as $project_id => $project ) {
                unset( $projects['my'][$project_id] );
                unset( $projects['all'][$project_id] );
                unset( $projects[''][$project_id] );
            }
        }
        foreach( $portfolios as $portfolio_id => $portfolio ) {
            if ( !in_array($portfolio_id, array('my','all')) ) continue;
            if ( is_array($projects[$portfolio_id]) ) {
                foreach( $projects[$portfolio_id] as $project_id => $project ) {
                    unset( $projects[''][$project_id] );
                }
            }
        }

        $realPortfolios = array_filter(array_keys($portfolios), function($key) {
            return !in_array($key, array('my','all'));
        });

        if ( array_key_exists('my', $portfolios) && count($projects['my']) < 1 ) {
            if ( count($programs) + count($realPortfolios) == 1 ) {
                unset($portfolios['my']);
            }
        }

        if ( array_key_exists('all', $portfolios) && count($projects['all']) < 1 ) {
            if ( count($programs) + count($realPortfolios) == 1 ) {
                unset($portfolios['all']);
            }
        }

        return array (
            'programs' => $programs,
            'portfolios' => $portfolios,
            'projects' => $projects,
            'admin_actions' => $this->getProjectNavigatorActions($projects),
            'portfolio_actions' => $this->getPortfolioActions($projects),
            'company_actions' => $this->getAddParticipantActions(),
            'settings_actions' => $this->getAdministrationActions()
        );
    }

    function getInviteUserUrl()
    {
        if ( !defined('INVITE_USERS_ANYBODY') || INVITE_USERS_ANYBODY !== false )
        {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
            return $method->getJSCall(array(), text(2001));
        }
        return '';
    }

    function getProjectNavigatorActions( $projects )
    {
        $actions = array();

        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED )
        {
            $portfolio = getFactory()->getObject('co_ProjectGroup');
            if ( count($projects) > 0 && getFactory()->getAccessPolicy()->can_create($portfolio) )
            {
                $method = new ObjectCreateNewWebMethod($portfolio);
                $method->setRedirectUrl('function(id){window.location=\'/pm/project-portfolio-\'+id;}');
                $actions[] = array (
                    'icon' => 'icon-briefcase',
                    'url' => $method->getJSCall(),
                    'name' => text('portfolio.new')
                );
            }
        }

        $inviteUrl = $this->getInviteUserUrl();
        if ( $inviteUrl != '' ) {
            $actions[] = array (
                'icon' => 'icon-user',
                'name' => text(2001),
                'url' => $inviteUrl
            );
        }

        return $actions;
    }

    function getPortfolioActions( $projects )
    {
        $actions = array();
        if ( count($projects) < 2 ) return $actions;

        $project_it = getSession()->getProjectIt();
        if ( $project_it->get('ProjectGroupId') < 1 ) return $actions;

        if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) ) {
            $actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  '/projects/welcome?portfolio='.$project_it->get('ProjectGroupId'),
                'name' => text('project.new')
            );
        }

        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED )
        {
            $portfolio = getFactory()->getObject('co_ProjectGroup');
            if ( getFactory()->getAccessPolicy()->can_modify($portfolio) )
            {
                $method = new ObjectModifyWebMethod(
                    $portfolio->getExact($project_it->get('ProjectGroupId'))
                );
                $actions[] = array (
                    'icon' => 'icon-briefcase',
                    'url' => $method->getJSCall(),
                    'name' => translate('Настройки')
                );
            }
        }

        return $actions;
    }

    function getAdministrationActions()
    {
        $actions = array();
        if ( getSession()->getUserIt()->get('IsAdmin') != 'Y' ) return $actions;

        $actions[] = array (
            'name' => translate('Пользователи'),
            'url' => '/admin/users.php'
        );

        $modules = array();
        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $modules['/admin/module/ee/usergroup'] = translate('user.groups.name');
        }
        $modules['/admin/'] = translate('Администрирование');
        if ( defined('ENTERPRISE_ENABLED') && ENTERPRISE_ENABLED ) {
            $modules['/admin/module/ee/projectgroup'] = translate('portfolios.name');
        }
        foreach( $modules as $ref => $title ) {
            $actions[] = array (
                'name' => $title,
                'url' => $ref
            );
        }

        $actions[] = array (
            'name' => text('projects.name'),
            'url' => '/admin/projects.php'
        );

        return $actions;
    }

    function getAddParticipantActions()
    {
        $actions = array();
        if ( getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Project')) )
        {
            $skip_welcome = getFactory()->getObject('UserSettings')->getSettingsValue('projects-welcome-page');
            $actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  $skip_welcome != 'off' && !defined('SKIP_WELCOME_PAGE')
                    ? '/projects/welcome'
                    : '/projects/new',
                'name' => text('project.new')
            );
        }

        return $actions;
    }

    private $page = null;
}