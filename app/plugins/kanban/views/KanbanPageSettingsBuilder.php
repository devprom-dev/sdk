<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class KanbanPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $setting = new PageTableSetting('KanbanRequestTable');
        $setting->setSorts( array('Priority') );
        $settings->add( $setting );
    }
}