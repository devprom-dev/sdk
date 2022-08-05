<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class ExampleEntitySettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $set )
    {
        $setting = new PageListSetting('ExampleEntityList');
        $setting->setVisibleColumns( array_merge(
        		array(
                    'Caption',
                    'URL'
                )
            )
       	);
        $set->add( $setting );
    }
}