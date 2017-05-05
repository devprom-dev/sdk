<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ProjectLogPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $object )
    {
        $setting = new PageTableSetting('ProjectLogTable');
        $setting->setRowsNumber( 20 );
        $setting->setFilters(array('start','action','participant'));
        $object->add( $setting );
        
        $setting = new PageListSetting('ProjectLogList');
        $setting->setGroup( 'ChangeDate' );
        $setting->setVisibleColumns( array('UserAvatar', 'SystemUser', 'Content', 'RecordModified') );
        $object->add( $setting );
    }
}