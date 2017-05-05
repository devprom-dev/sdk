<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class VersionPageSettingBuilder extends PageSettingBuilder
{
    function build( PageSettingSet & $settings )
    {
        $setting = new PageListSetting('VersionList');
        $setting->setGroup( 'none' );
        //$setting->setVisibleColumns( array('UID', 'Caption', 'State', 'Priority') );
        $settings->add( $setting );

        $setting = new PageTableSetting('VersionTable');
        $settings->add( $setting );
    }
}