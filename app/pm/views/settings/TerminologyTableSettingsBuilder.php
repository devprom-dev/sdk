<?php

include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class TerminologyTableSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $object )
    {
        $setting = new PageTableSetting('TerminologyTable');
        
        $setting->setRowsNumber( 60 );

        $object->add( $setting );
    }
}