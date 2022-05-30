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
                'name' => text('guide.mobile.app'),
                'click' => "javascript: workflowModify({'form_url':'/widget/mobile/".$language."','class_name':'','entity_ref':'','object_id':'','can_delete':'false','can_modify':'false','delete_reason':null,'width':520}, donothing);"
            ),
            array(),
            array (
                'name' => text('guide.support'),
                'click' => "javascript: workflowModify({'form_url':'/widget/support/".$language."','class_name':'','entity_ref':'','object_id':'','can_delete':'false','can_modify':'false','delete_reason':null,'width':520}, donothing);"
            )
        );
    }

    function getProjectNavigationParms()
    {
        return array (
            'admin_actions' => $this->getActions(),
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

    function getActions()
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
        if ( defined('PERMISSIONS_ENABLED') && PERMISSIONS_ENABLED ) {
            $modules['/admin/module/permissions/usergroup'] = translate('user.groups.name');
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