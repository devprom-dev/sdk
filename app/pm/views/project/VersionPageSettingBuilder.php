<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class VersionPageSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('VersionTree');
        $setting->setGroup( 'none' );
        $setting->setVisibleColumns( array('Caption', 'RecentComment','Planned', 'Fact', 'Progress') );
        $settings->add( $setting );

        $setting = new PageTableSetting('VersionTable');
        $settings->add( $setting );
    }
}