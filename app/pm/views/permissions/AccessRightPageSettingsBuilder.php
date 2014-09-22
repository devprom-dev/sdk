<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class AccessRightPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $object )
    {
        $setting = new PageTableSetting('AccessRightTable');
        
        $setting->setRowsNumber( 60 );

        $object->add( $setting );
    }
}