<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class KanbanPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageTableSetting('KanbanRequestTable');
        $setting->setSorts( array('Priority') );
        $setting->setFilters(array('type','priority'));
        $settings->add( $setting );

        $setting = new ReportSetting('avgleadtime');
        $setting->setFilters( array('type','priority','modifiedafter','owner','estimation') );
        $settings->add( $setting );
    }
}