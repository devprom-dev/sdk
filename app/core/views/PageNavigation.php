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
        $timestamp = getSession()->getUserPicTimestamp();

        return array (
            'class' => 'header_popup',
            'title' => '<span class="profile-avatar" style="background: url(\'/images/userpics-middle.png?v='.$timestamp.'\') no-repeat -'.($column * $size).'px '. (max(0,-1 * $row * $size)).'px;" title="'.text(2637).'"></span>',
            'items' => $actions,
        );
    }

    function getHelpActions()
    {
        $language = strtolower(getSession()->getLanguageUid());
        return array(
            array(),
            array (
                'name' => text('guide.support'),
                'click' => "javascript: workflowModify({'form_url':'/widget/support/".$language."','class_name':'','entity_ref':'','object_id':'','can_delete':'false','can_modify':'false','delete_reason':null,'width':520}, donothing);"
            )
        );
    }

    function getProjectNavigationParms()
    {
        $programs = array();
        $projects = array();
        $sortIndex = 0;

        if ( getFactory()->getObject('User')->getAttributeType('GroupId') != '' )
        {
            $program_it = getFactory()->getObject('Program')->getRegistry()->Query(
                array(
                    new ProjectAccessibleActiveVpdPredicate()
                )
            );

            while ( !$program_it->end() )
            {
                $query_parms = array (
                    new ProjectStatePredicate('active'),
                    new FilterInPredicate(preg_split('/,/', $program_it->get('LinkedProject')))
                );

                if ( $program_it->get('IsParticipant') < 1 ) {
                    $query_parms[] = new ProjectParticipatePredicate();
                }

                $linked_it = $program_it->get('LinkedProject') != ''
                    ? getFactory()->getObject('Project')->getRegistry()->Query($query_parms)
                    : getFactory()->getObject('Project')->getEmptyIterator();

                while ( !$linked_it->end() )
                {
                    if ( $program_it->getId() == $linked_it->getId() ) {
                        $linked_it->moveNext();
                        continue;
                    }

                    $projects[$program_it->get('CodeName')][$linked_it->get('CodeName')] = array (
                        'name' => $linked_it->getDisplayName(),
                        'url' => '/pm/'.$linked_it->get('CodeName'),
                        'sort' => $sortIndex++
                    );
                    $linked_it->moveNext();
                }

                if ( count($projects[$program_it->get('CodeName')]) > 0 ) {
                    $programs[$program_it->get('CodeName')] = array (
                        'name' => $program_it->getDisplayName(),
                        'url' => '/pm/'.$program_it->get('CodeName'),
                        'uid' => 'program-portfolio'
                    );
                }
                $program_it->moveNext();
            }
        }

        $portfolios = array();

        $portfolio_it = getFactory()->getObject('Portfolio')->getAll();
        while ( !$portfolio_it->end() )
        {
            if ( !getFactory()->getAccessPolicy()->can_read($portfolio_it) ) {
                $portfolio_it->moveNext(); continue;
            }

            if ( $portfolio_it->get('CodeName') != 'all' || !defined('PERMISSIONS_ENABLED') )
            {
                $linked_it = $portfolio_it->get('LinkedProject') != ''
                    ? getFactory()->getObject('Project')->getRegistry()->Query(
                        array (
                            new ProjectStatePredicate('active'),
                            new FilterInPredicate(preg_split('/,/', $portfolio_it->get('LinkedProject')))
                        )
                    )
                    : getFactory()->getObject('Project')->getEmptyIterator();

                while ( !$linked_it->end() ) {
                    if ( $portfolio_it->getId() == $linked_it->getId() ) {
                        $linked_it->moveNext();
                        continue;
                    }
                    $projects[$portfolio_it->get('CodeName')][$linked_it->get('CodeName')] = array (
                        'name' => $linked_it->getDisplayName(),
                        'url' => '/pm/'.$linked_it->get('CodeName'),
                        'sort' => $sortIndex++
                    );
                    $linked_it->moveNext();
                }
            }

            if ( in_array($portfolio_it->get('CodeName'), array('all', 'my')) || count($projects[$portfolio_it->get('CodeName')]) > 0 )
            {
                $portfolios[$portfolio_it->get('CodeName')] = array (
                    'name' => $portfolio_it->getDisplayName(),
                    'url' => '/pm/'.$portfolio_it->get('CodeName'),
                    'uid' => 'program-portfolio'
                );
            }
            $portfolio_it->moveNext();
        }

        $query = array(
            new ProjectStatePredicate('active'),
            new SortAttributeClause('Importance'),
            new SortAttributeClause('Caption')
        );
        if ( defined('PERMISSIONS_ENABLED') ) {
            $query[] = new ProjectParticipatePredicate();
        }
        $linked_it = getFactory()->getObject('Project')->getRegistry()->Query($query);

        while ( !$linked_it->end() )
        {
            $projects[''][$linked_it->get('CodeName')] = array (
                'name' => $linked_it->getDisplayName(),
                'url' => '/pm/'.$linked_it->get('CodeName'),
                'sort' => $sortIndex++
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

        if ( array_key_exists('my', $portfolios) ) {
            $allPrtfolio = $portfolios['all'];
            if ( is_array($allPrtfolio) ) {
                unset($portfolios['all']);
                $portfolios[] = $allPrtfolio;
            }
        }

        foreach( $projects as $portolio => $group ) {
            usort($projects[$portfolio], function($left, $right) {
                return $left['sort'] > $right['sort'];
            });
        }

        return array (
            'programs' => $programs,
            'portfolios' => $portfolios,
            'projects' => $projects,
            'admin_actions' => $this->getActions($projects),
            'company_actions' => $this->getAddParticipantActions(),
            'settings_actions' => $this->getAdministrationLinks()
        );
    }

    function getInviteUserUrl()
    {
        if ( !defined('INVITE_USERS_ANYBODY') || INVITE_USERS_ANYBODY !== false ) {
            $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Invitation'));
            if ( $method->hasAccess() ) return $method->getJSCall(array(), text(2001));
        }
        return '';
    }

    function getActions( $projects )
    {
        $actions = array();

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

    function getAdministrationLinks()
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
        $modules['/admin/activity.php'] = translate('Администрирование');
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
            $url = $skip_welcome != 'off' && !defined('SKIP_WELCOME_PAGE')
                ? '/projects/welcome'
                : '/projects/new';

            $projectIt = getSession()->getProjectIt();
            if ( $projectIt->IsPortfolio() && $projectIt->get('ProjectGroupId') != '' ) {
                $url .= '?portfolio='.$projectIt->get('ProjectGroupId');
            }

            $actions[] = array (
                'icon' => 'icon-plus',
                'url' =>  $url,
                'name' => text('project.new')
            );
        }

        return $actions;
    }

    private $page = null;
}