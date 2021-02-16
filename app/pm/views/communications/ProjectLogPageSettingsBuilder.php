<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ProjectLogPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $object )
    {
        $setting = new PageTableSetting('ProjectLogTable');
        $setting->setRowsNumber( 20 );
        $object->add( $setting );
        
        $setting = new PageListSetting('ProjectLogList');
        $setting->setGroup( 'ChangeDate.D' );
        $setting->setVisibleColumns( array('UserAvatar', 'SystemUser', 'Content', 'RecordModified') );
        $object->add( $setting );
    }
}