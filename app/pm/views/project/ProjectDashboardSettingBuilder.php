<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ProjectDashboardSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('ProjectDashboardList');
        $setting->setVisibleColumns( array(
            'UID', 'Caption', 'Description', 'Importance', 'StartDate', 'FinishDate',
            'Features', 'Deadlines', 'SpentHoursWeek', 'Progress', 'RecentBuild', 'Budget'
        ) );
        $settings->add( $setting );
    }
}