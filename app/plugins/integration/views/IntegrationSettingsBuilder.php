<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class IntegrationSettingsBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $set )
    {
        $setting = new PageListSetting('IntegrationList');
        $setting->setVisibleColumns( array_merge(
        		array(
                    'Caption',
                    'StatusText',
                    'URL',
                    'Type'
                )
            )
       	);
        $set->add( $setting );
    }
}