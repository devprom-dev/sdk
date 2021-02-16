<?php
include_once SERVER_ROOT_PATH . "pm/classes/project/ProjectModelExtendedBuilder.php";
include "ProjectDashboardTable.php";
include "ProjectDashboardSettingBuilder.php";

class ProjectDashboardPage extends PMPage
{
    function __construct()
    {
        getSession()->addBuilder( new ProjectModelExtendedBuilder() );
        getSession()->addBuilder( new ProjectDashboardSettingBuilder() );
        parent::__construct();
    }

    function getObject() {
 		return new Project(
            getSession()->getProjectIt()->get('CodeName') == 'all'
                ? null : new ProjectLinkedRegistry()
        );
 	}
 	
 	function getTable() {
 		return new ProjectDashboardTable( $this->getObject() );
 	}
 	
    function getPageWidgets() {
        return array('project-plan-hierarchy');
    }
}
