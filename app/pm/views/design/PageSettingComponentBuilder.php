<?php
include_once SERVER_ROOT_PATH.'pm/views/common/PageSettingBuilder.php';

class PageSettingComponentBuilder extends PageSettingBuilder
{
    public function build( PageSettingSet & $settings )
    {
        $feature = getFactory()->getObject('Component');

        $setting = new PageListSetting('ComponentTreeGrid');
        $setting->setVisibleColumns(
            array(
                'UID', 'Caption'
            )
        );
        $settings->add( $setting );

        $setting = new PageListSetting('ComponentList');
        $setting->setVisibleColumns(
            array_diff(
                array_merge(
                    array( 'UID', 'Caption' ),
                    $feature->getAttributesByGroup('trace')
                ),
                $feature->getAttributesByGroup('system')
            )
        );
        $settings->add( $setting );
    }
}