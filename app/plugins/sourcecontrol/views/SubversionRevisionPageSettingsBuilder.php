<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class SubversionRevisionPageSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $object )
    {
        $setting = new PageTableSetting('SubversionRevisionTable');
        
        $setting->setRowsNumber( 20 );

        $object->add( $setting );
    }
}